<?php declare(strict_types=1);

namespace Tests\Torr\Hosting\Command;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Torr\Hosting\BuildInfo\BuildInfoStorage;
use Torr\Hosting\Command\BuildHooksCommand;
use Torr\Hosting\Command\DeployAppHooksCommand;
use Torr\Hosting\Command\DeployContainerHooksCommand;
use Torr\Hosting\Command\ShowBuildInfoCommand;
use Torr\Hosting\Command\ValidateAppCommand;
use Torr\Hosting\Deployment\BuildHookInterface;
use Torr\Hosting\Deployment\DeployAppHookInterface;
use Torr\Hosting\Deployment\DeployContainerHookInterface;
use Torr\Hosting\Deployment\Exception\HookFailedException;
use Torr\Hosting\Deployment\HookRunners;
use Torr\Hosting\Event\ValidateAppEvent;

/**
 * Runs every command through a real console application, to verify that the invokable
 * commands are wired up correctly and return a proper exit code.
 *
 * @internal
 */
final class CommandExecutionTest extends TestCase
{
	/**
	 */
	public function testBuildHooksCommand () : void
	{
		$tester = $this->createTester(new BuildHooksCommand(new HookRunners([], [], [], new NullLogger())));

		self::assertSame(Command::SUCCESS, $tester->run(["command" => "hosting:hook:build"]));
		self::assertStringContainsString("Ran all build hooks.", $tester->getDisplay());
	}

	/**
	 */
	public function testDeployAppHooksCommand () : void
	{
		$tester = $this->createTester(new DeployAppHooksCommand(new HookRunners([], [], [], new NullLogger())));

		self::assertSame(Command::SUCCESS, $tester->run(["command" => "hosting:hook:deploy-app"]));
		self::assertStringContainsString("Ran all deploy app hooks.", $tester->getDisplay());
	}

	/**
	 */
	public function testDeployContainerHooksCommand () : void
	{
		$tester = $this->createTester(new DeployContainerHooksCommand(new HookRunners([], [], [], new NullLogger())));

		self::assertSame(Command::SUCCESS, $tester->run(["command" => "hosting:hook:deploy-container"]));

		$display = $tester->getDisplay();
		self::assertStringContainsString("Ran all deploy container hooks.", $display);

		// don't assert on the full sentence here: the caution block is word-wrapped, so any
		// substring spanning the wrap point would never match — not even when the warning is shown.
		self::assertStringNotContainsString("other names are deprecated", $display);
	}

	/**
	 * The command may still be called via its deprecated alias, but must warn about it.
	 */
	public function testDeployContainerHooksCommandWarnsAboutTheDeprecatedAlias () : void
	{
		$this->expectUserDeprecationMessage("Since 21torr/hosting 4.2.1: Always call this command with `hosting:hook:deploy-container`, all other names are deprecated");

		$tester = $this->createTester(new DeployContainerHooksCommand(new HookRunners([], [], [], new NullLogger())));

		self::assertSame(Command::SUCCESS, $tester->run(["command" => "hosting:hook:deploy"]));
		self::assertStringContainsString("Ran all deploy container hooks.", $tester->getDisplay());
	}

	/**
	 * An abbreviation of the canonical name is not a deprecated alias, so it must not warn.
	 */
	public function testDeployContainerHooksCommandDoesNotWarnAboutAbbreviations () : void
	{
		$tester = $this->createTester(new DeployContainerHooksCommand(new HookRunners([], [], [], new NullLogger())));

		self::assertSame(Command::SUCCESS, $tester->run(["command" => "hosting:hook:deploy-cont"]));

		$display = $tester->getDisplay();
		self::assertStringContainsString("Ran all deploy container hooks.", $display);
		self::assertStringNotContainsString("other names are deprecated", $display);
	}

	/**
	 * A failing hook must be reported and must lead to an error exit code.
	 */
	public function testBuildHooksCommandFailsForFailingHooks () : void
	{
		$hook = self::createStub(BuildHookInterface::class);
		$hook->method("getLabel")->willReturn("failing hook");
		$hook->method("runPostBuild")->willThrowException(new HookFailedException("build exploded"));

		$tester = $this->createTester(new BuildHooksCommand(new HookRunners([$hook], [], [], new NullLogger())));

		self::assertSame(Command::FAILURE, $tester->run(["command" => "hosting:hook:build"]));

		$display = $tester->getDisplay();
		self::assertStringContainsString("build exploded", $display);
		self::assertStringNotContainsString("Ran all build hooks.", $display);
	}

	/**
	 * A failing hook must be reported and must lead to an error exit code.
	 */
	public function testDeployAppHooksCommandFailsForFailingHooks () : void
	{
		$hook = self::createStub(DeployAppHookInterface::class);
		$hook->method("getLabel")->willReturn("failing hook");
		$hook->method("runDeployApp")->willThrowException(new HookFailedException("app exploded"));

		$tester = $this->createTester(new DeployAppHooksCommand(new HookRunners([], [], [$hook], new NullLogger())));

		self::assertSame(Command::FAILURE, $tester->run(["command" => "hosting:hook:deploy-app"]));

		$display = $tester->getDisplay();
		self::assertStringContainsString("app exploded", $display);
		self::assertStringNotContainsString("Ran all deploy app hooks.", $display);
	}

	/**
	 * A failing hook must be reported and must lead to an error exit code.
	 */
	public function testDeployContainerHooksCommandFailsForFailingHooks () : void
	{
		$hook = self::createStub(DeployContainerHookInterface::class);
		$hook->method("getLabel")->willReturn("failing hook");
		$hook->method("runDeployContainer")->willThrowException(new HookFailedException("container exploded"));

		$tester = $this->createTester(new DeployContainerHooksCommand(new HookRunners([], [$hook], [], new NullLogger())));

		self::assertSame(Command::FAILURE, $tester->run(["command" => "hosting:hook:deploy-container"]));

		$display = $tester->getDisplay();
		self::assertStringContainsString("container exploded", $display);
		self::assertStringNotContainsString("Ran all deploy container hooks.", $display);
	}

	/**
	 */
	public function testShowBuildInfoCommand () : void
	{
		$storage = new BuildInfoStorage(
			new EventDispatcher(),
			new Filesystem(),
			__DIR__ . "/this-file-does-not-exist.json",
		);
		$tester = $this->createTester(new ShowBuildInfoCommand($storage));

		self::assertSame(Command::SUCCESS, $tester->run(["command" => "hosting:build:info"]));
		self::assertStringContainsString("Hosting: Build Info", $tester->getDisplay());
	}

	/**
	 */
	public function testValidateAppCommandSucceedsForValidApps () : void
	{
		$tester = $this->createTester(new ValidateAppCommand(new EventDispatcher(), new NullLogger()));

		self::assertSame(Command::SUCCESS, $tester->run(["command" => "hosting:validate-app"]));
		self::assertStringContainsString("App is valid", $tester->getDisplay());
	}

	/**
	 */
	public function testValidateAppCommandFailsForInvalidApps () : void
	{
		$dispatcher = new EventDispatcher();
		$dispatcher->addListener(
			ValidateAppEvent::class,
			static fn (ValidateAppEvent $event) => $event->markAppAsInvalid("some check failed"),
		);

		$tester = $this->createTester(new ValidateAppCommand($dispatcher, new NullLogger()));

		self::assertSame(Command::FAILURE, $tester->run(["command" => "hosting:validate-app"]));
		self::assertStringContainsString("Validation failed: some check failed", $tester->getDisplay());
	}

	/**
	 * Exceptions in the listeners must be caught and reported as failed validation.
	 */
	public function testValidateAppCommandFailsForFailingListeners () : void
	{
		$dispatcher = new EventDispatcher();
		$dispatcher->addListener(
			ValidateAppEvent::class,
			static function () : never
			{
				throw new \RuntimeException("listener exploded");
			},
		);

		$tester = $this->createTester(new ValidateAppCommand($dispatcher, new NullLogger()));

		self::assertSame(Command::FAILURE, $tester->run(["command" => "hosting:validate-app"]));
		self::assertStringContainsString("Validation failed: listener exploded", $tester->getDisplay());
	}

	/**
	 * Registers the invokable command in an application, the same way the console command loader
	 * does it at runtime.
	 */
	private function createTester (object $command) : ApplicationTester
	{
		$application = new Application();
		$application->setAutoExit(false);
		$application->setCatchExceptions(false);
		$application->addCommand($command);

		return new ApplicationTester($application);
	}
}
