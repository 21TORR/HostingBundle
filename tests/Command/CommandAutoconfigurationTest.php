<?php declare(strict_types=1);

namespace Tests\Torr\Hosting\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Torr\Hosting\Command\BuildHooksCommand;
use Torr\Hosting\Command\DeployAppHooksCommand;
use Torr\Hosting\Command\DeployContainerHooksCommand;
use Torr\Hosting\Command\ShowBuildInfoCommand;
use Torr\Hosting\Command\ValidateAppCommand;

/**
 * Verifies that all commands are still registered as console commands with their names, aliases
 * and descriptions, even though they are plain invokable classes that don't extend
 * `Symfony\Component\Console\Command\Command` anymore.
 *
 * @internal
 */
final class CommandAutoconfigurationTest extends TestCase
{
	private const array COMMANDS = [
		BuildHooksCommand::class,
		DeployAppHooksCommand::class,
		DeployContainerHooksCommand::class,
		ShowBuildInfoCommand::class,
		ValidateAppCommand::class,
	];

	/**
	 */
	public function testAllCommandsAreRegisteredWithTheirNamesAndAliases () : void
	{
		$container = $this->compileContainer();

		self::assertEquals(
			[
				"hosting:hook:build" => BuildHooksCommand::class . ".command",
				"hosting:hook:deploy-app" => DeployAppHooksCommand::class . ".command",
				"hosting:hook:deploy-container" => DeployContainerHooksCommand::class . ".command",
				"hosting:hook:deploy" => DeployContainerHooksCommand::class . ".command",
				"hosting:build:info" => ShowBuildInfoCommand::class . ".command",
				"hosting:validate-app" => ValidateAppCommand::class . ".command",
			],
			$container->getDefinition("console.command_loader")->getArgument(1),
		);
	}

	/**
	 * Every command must have a description, as it is displayed in `bin/console list`.
	 */
	public function testAllCommandsHaveADescription () : void
	{
		$container = $this->compileContainer();
		$descriptions = [];

		foreach (self::COMMANDS as $class)
		{
			// the lazy definition is only registered if the command has a description
			$lazyDefinition = $container->getDefinition(\sprintf(".%s.command.lazy", $class));
			$descriptions[$class] = $lazyDefinition->getArgument(2);
		}

		self::assertSame(
			[
				BuildHooksCommand::class => "Runs the hooks for 'after the build finished'",
				DeployAppHooksCommand::class => "Runs the hooks for deploying a complete application.",
				DeployContainerHooksCommand::class => "Runs the hooks for deploying a single container",
				ShowBuildInfoCommand::class => "Shows the current build info",
				ValidateAppCommand::class => "Validates the app configuration, for usage in the CI before deployment",
			],
			$descriptions,
		);
	}

	/**
	 * Compiles a container that registers the commands the same way the bundle's `config/services.yaml`
	 * does (autoconfigured via the `src/*` resource glob), combined with the `#[AsCommand]`
	 * autoconfiguration + compiler pass of `Symfony\Component\Console\ConsoleBundle`.
	 */
	private function compileContainer () : ContainerBuilder
	{
		$container = new ContainerBuilder();

		$container->registerAttributeForAutoconfiguration(
			AsCommand::class,
			static function (ChildDefinition $definition, AsCommand $attribute) : void
			{
				$definition->addTag("console.command", [
					"command" => $attribute->name,
					"description" => $attribute->description,
					"help" => $attribute->help,
				]);
			},
		);
		$container->addCompilerPass(new AddConsoleCommandPass(), PassConfig::TYPE_BEFORE_REMOVING);

		foreach (self::COMMANDS as $class)
		{
			$container->register($class)
				->setAutoconfigured(true);
		}

		$container->compile();

		return $container;
	}
}
