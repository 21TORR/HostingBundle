<?php declare(strict_types=1);

namespace Tests\Torr\Hosting\Deployment;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Torr\Hosting\Deployment\DeployContainerHookInterface;
use Torr\Hosting\Deployment\DeployHookInterface;
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

		$runners = new HookRunners([], [$hook], []);
		$runners->runDeployContainerHooks($this->createTaskCli());
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

		$runners = new HookRunners([], [$hook], []);
		$runners->runDeployContainerHooks($this->createTaskCli());
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

		$runners = new HookRunners([], [$legacyHook, $newHook], []);
		$runners->runDeployContainerHooks($this->createTaskCli());
	}

	/**
	 */
	private function createTaskCli () : TaskCli
	{
		return new TaskCli(new ArrayInput([]), new NullOutput());
	}
}