<?php declare(strict_types=1);

namespace Tests\Torr\Hosting\Command;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\ConsoleBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Torr\Hosting\BuildInfo\BuildInfoStorage;
use Torr\Hosting\Deployment\HookRunners;

/**
 * Verifies that all commands are still registered as console commands with their names, aliases
 * and descriptions, even though they are plain invokable classes that don't extend
 * `Symfony\Component\Console\Command\Command` anymore.
 *
 * @internal
 */
final class CommandAutoconfigurationTest extends TestCase
{
	/**
	 */
	public function testAllCommandsAreRegisteredWithTheirNamesAndAliases () : void
	{
		$loader = $this->compileCommandLoader();

		self::assertEqualsCanonicalizing(
			[
				"hosting:hook:build",
				"hosting:hook:deploy-app",
				"hosting:hook:deploy-container",
				"hosting:hook:deploy",
				"hosting:build:info",
				"hosting:validate-app",
			],
			$loader->getNames(),
		);

		self::assertSame(
			["hosting:hook:deploy"],
			$loader->get("hosting:hook:deploy-container")->getAliases(),
		);
	}

	/**
	 * Every command must have a description, as it is displayed in `bin/console list`.
	 */
	public function testAllCommandsHaveADescription () : void
	{
		$loader = $this->compileCommandLoader();
		$descriptions = [];

		foreach ($loader->getNames() as $name)
		{
			$descriptions[$name] = $loader->get($name)->getDescription();
		}

		self::assertSame(
			[
				"hosting:hook:build" => "Runs the hooks for 'after the build finished'",
				"hosting:hook:deploy-app" => "Runs the hooks for deploying a complete application.",
				"hosting:hook:deploy-container" => "Runs the hooks for deploying a single container",
				"hosting:hook:deploy" => "Runs the hooks for deploying a single container",
				"hosting:build:info" => "Shows the current build info",
				"hosting:validate-app" => "Validates the app configuration, for usage in the CI before deployment",
			],
			$descriptions,
		);
	}

	/**
	 * Guards against a newly added command that is missing its `#[AsCommand]` attribute, and
	 * therefore silently wouldn't be registered at all.
	 */
	public function testEveryCommandClassIsRegistered () : void
	{
		$loader = $this->compileCommandLoader();

		foreach ($this->findCommandClasses() as $class)
		{
			$attribute = (new \ReflectionClass($class))->getAttributes(AsCommand::class)[0] ?? null;

			self::assertNotNull($attribute, \sprintf("%s must have an #[AsCommand] attribute", $class));

			// aliases and the hidden flag are folded into the name, separated by "|"
			$name = explode("|", $attribute->newInstance()->name)[0];

			self::assertTrue($loader->has($name), \sprintf("%s must be registered as `%s`", $class, $name));
		}
	}

	/**
	 * Compiles the command loader of a container that registers the commands the same way the
	 * bundle's `config/services.yaml` does (autoconfigured via the `src/*` resource glob), using
	 * the real compiler passes of `Symfony\Component\Console\ConsoleBundle`.
	 *
	 * The `console.command` tag is intentionally left bare: `AddConsoleCommandPass` reads the
	 * name, aliases and description from the `#[AsCommand]` attribute itself, so this asserts
	 * against the actual attributes instead of a copy of the bundle's autoconfiguration.
	 */
	private function compileCommandLoader () : CommandLoaderInterface
	{
		$container = new ContainerBuilder();
		new ConsoleBundle()->build($container);

		$this->registerCommandDependencies($container);

		foreach ($this->findCommandClasses() as $class)
		{
			$container->register($class)
				->setAutowired(true)
				->addTag("console.command");
		}

		$container->compile();

		$loader = $container->get("console.command_loader");
		\assert($loader instanceof CommandLoaderInterface);

		return $loader;
	}

	/**
	 * Registers everything the commands autowire, so that the loader can actually build them.
	 * A command without a description isn't wrapped in a `LazyCommand`, so it gets instantiated
	 * as soon as it is fetched from the loader.
	 */
	private function registerCommandDependencies (ContainerBuilder $container) : void
	{
		$container->register(EventDispatcher::class);
		$container->register(NullLogger::class);
		$container->register(Filesystem::class);
		$container->setAlias(PsrEventDispatcherInterface::class, EventDispatcher::class);
		$container->setAlias(EventDispatcherInterface::class, EventDispatcher::class);
		$container->setAlias(LoggerInterface::class, NullLogger::class);

		$container->register(HookRunners::class)
			->setAutowired(true);

		$container->register(BuildInfoStorage::class)
			->setAutowired(true)
			->setArgument('$filePath', __DIR__ . "/this-file-does-not-exist.json");
	}

	/**
	 * @return list<class-string>
	 */
	private function findCommandClasses () : array
	{
		$files = glob(__DIR__ . "/../../src/Command/*.php");
		self::assertNotFalse($files);

		$classes = array_map(
			static function (string $file) : string
			{
				/** @var class-string $class */
				$class = "Torr\\Hosting\\Command\\" . basename($file, ".php");

				self::assertTrue(class_exists($class), \sprintf("Class %s must exist", $class));

				return $class;
			},
			$files,
		);

		self::assertNotEmpty($classes);

		return array_values($classes);
	}
}
