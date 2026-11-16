<?php declare(strict_types=1);

namespace Tests\Torr\Hosting\Deployment;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Torr\Hosting\Deployment\BuildHookInterface;
use Torr\Hosting\Deployment\DeployAppHookInterface;
use Torr\Hosting\Deployment\DeployContainerHookInterface;
use Torr\Hosting\Deployment\DeployHookInterface;
use Torr\Hosting\Deployment\Exception\HookFailedException;
use Torr\Hosting\Deployment\HookRunners;
use Torr\Hosting\Deployment\TaskCli;

/**
 * @internal
 */
final class HookRunnersTest extends TestCase
{
	/**
	 * The new interface must be called via its own method and must not trigger
	 * the BC deprecation layer.
	 */
	public function testRunDeployContainerHooksCallsNewInterface () : void
	{
		$hook = $this->createMock(DeployContainerHookInterface::class);
		$hook
			->expects(self::once())
			->method("getLabel")
			->willReturn("new hook");
		$hook
			->expects(self::once())
			->method("runDeployContainer");

		$runners = new HookRunners([], [$hook], [], new NullLogger());
		self::assertTrue($runners->runDeployContainerHooks($this->createTaskCli()));
	}

	/**
	 * Legacy hooks implementing the deprecated {@see DeployHookInterface} must still
	 * be run via their old `runPostDeployment()` method and must trigger a deprecation.
	 */
	public function testRunDeployContainerHooksBcLayerCallsOldInterface () : void
	{
		$this->expectUserDeprecationMessageMatches(
			"#^Since 21torr/hosting 4\\.2\\.1: Using 'deploy hooks' is deprecated, use 'DeployContainerHook' instead\\. Used in '.*'$#",
		);

		$hook = $this->createMock(DeployHookInterface::class);
		$hook
			->expects(self::once())
			->method("getLabel")
			->willReturn("legacy hook");
		$hook
			->expects(self::once())
			->method("runPostDeployment");

		$runners = new HookRunners([], [$hook], [], new NullLogger());
		self::assertTrue($runners->runDeployContainerHooks($this->createTaskCli()));
	}

	/**
	 * A mix of legacy and new hooks must each be dispatched to their correct method.
	 */
	public function testRunDeployContainerHooksBcLayerWithMixedHooks () : void
	{
		$this->expectUserDeprecationMessageMatches(
			"#^Since 21torr/hosting 4\\.2\\.1: Using 'deploy hooks' is deprecated, use 'DeployContainerHook' instead\\.#",
		);

		$legacyHook = $this->createMock(DeployHookInterface::class);
		$legacyHook->method("getLabel")->willReturn("legacy hook");
		$legacyHook
			->expects(self::once())
			->method("runPostDeployment");

		$newHook = $this->createMock(DeployContainerHookInterface::class);
		$newHook->method("getLabel")->willReturn("new hook");
		$newHook
			->expects(self::once())
			->method("runDeployContainer");

		$runners = new HookRunners([], [$legacyHook, $newHook], [], new NullLogger());
		self::assertTrue($runners->runDeployContainerHooks($this->createTaskCli()));
	}

	/**
	 * A failed build hook must be printed, logged as error and must stop the run.
	 */
	public function testRunBuildHooksReportsFailedHook () : void
	{
		$exception = new HookFailedException("build exploded");

		$failingHook = self::createStub(BuildHookInterface::class);
		$failingHook->method("getLabel")->willReturn("failing hook");
		$failingHook->method("runPostBuild")->willThrowException($exception);

		$skippedHook = $this->createMock(BuildHookInterface::class);
		$skippedHook->expects(self::never())->method("runPostBuild");

		$output = new BufferedOutput();
		$runners = new HookRunners([$failingHook, $skippedHook], [], [], $this->createLoggerExpectingError($exception));

		self::assertFalse($runners->runBuildHooks(new TaskCli(new ArrayInput([]), $output)));
		self::assertStringContainsString("Build hook 'failing hook' failed: build exploded", $output->fetch());
	}

	/**
	 * A failed deploy container hook must be printed, logged as error and must stop the run.
	 */
	public function testRunDeployContainerHooksReportsFailedHook () : void
	{
		$exception = new HookFailedException("container exploded");

		$failingHook = self::createStub(DeployContainerHookInterface::class);
		$failingHook->method("getLabel")->willReturn("failing hook");
		$failingHook->method("runDeployContainer")->willThrowException($exception);

		$skippedHook = $this->createMock(DeployContainerHookInterface::class);
		$skippedHook->expects(self::never())->method("runDeployContainer");

		$output = new BufferedOutput();
		$runners = new HookRunners([], [$failingHook, $skippedHook], [], $this->createLoggerExpectingError($exception));

		self::assertFalse($runners->runDeployContainerHooks(new TaskCli(new ArrayInput([]), $output)));
		self::assertStringContainsString("Deploy Container hook 'failing hook' failed: container exploded", $output->fetch());
	}

	/**
	 * A failed deploy app hook must be printed, logged as error and must stop the run.
	 */
	public function testRunDeployAppHooksReportsFailedHook () : void
	{
		$exception = new HookFailedException("app exploded");

		$failingHook = self::createStub(DeployAppHookInterface::class);
		$failingHook->method("getLabel")->willReturn("failing hook");
		$failingHook->method("runDeployApp")->willThrowException($exception);

		$skippedHook = $this->createMock(DeployAppHookInterface::class);
		$skippedHook->expects(self::never())->method("runDeployApp");

		$output = new BufferedOutput();
		$runners = new HookRunners([], [], [$failingHook, $skippedHook], $this->createLoggerExpectingError($exception));

		self::assertFalse($runners->runDeployAppHooks(new TaskCli(new ArrayInput([]), $output)));
		self::assertStringContainsString("Deploy App hook 'failing hook' failed: app exploded", $output->fetch());
	}

	/**
	 * Only the dedicated hook exception is handled, everything else is an unexpected error
	 * and must not be swallowed.
	 */
	public function testRunBuildHooksDoesNotCatchUnrelatedExceptions () : void
	{
		$hook = self::createStub(BuildHookInterface::class);
		$hook->method("getLabel")->willReturn("broken hook");
		$hook->method("runPostBuild")->willThrowException(new \LogicException("unexpected"));

		$runners = new HookRunners([$hook], [], [], new NullLogger());

		$this->expectException(\LogicException::class);
		$runners->runBuildHooks($this->createTaskCli());
	}

	/**
	 */
	private function createLoggerExpectingError (\Throwable $exception) : LoggerInterface
	{
		$logger = $this->createMock(LoggerInterface::class);
		$logger
			->expects(self::once())
			->method("error")
			->with(
				self::isString(),
				self::callback(static fn (array $context) => $exception === ($context["exception"] ?? null)),
			);

		return $logger;
	}

	/**
	 */
	private function createTaskCli () : TaskCli
	{
		return new TaskCli(new ArrayInput([]), new NullOutput());
	}
}
