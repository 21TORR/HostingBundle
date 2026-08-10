<?php declare(strict_types=1);

namespace Tests\Torr\Hosting\Deployment;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tests\Torr\Hosting\Deployment\Fixtures\FixtureBuildHook;
use Tests\Torr\Hosting\Deployment\Fixtures\FixtureDeployAppHook;
use Tests\Torr\Hosting\Deployment\Fixtures\FixtureDeployContainerHook;
use Tests\Torr\Hosting\Deployment\Fixtures\FixtureLegacyDeployHook;
use Torr\Hosting\Deployment\BuildHookInterface;
use Torr\Hosting\Deployment\DeployAppHookInterface;
use Torr\Hosting\Deployment\DeployContainerHookInterface;
use Torr\Hosting\Deployment\DeployHookInterface;
use Torr\Hosting\Deployment\HookRunners;

/**
 * Verifies that the hook interfaces (build / deploy-container / deploy-app, including
 * the deprecated deploy-container BC interface) are autoconfigured with the correct
 * tags and wired into {@see HookRunners} by the container, the same way the bundle's
 * `config/services.yaml` sets up `autoconfigure` + `autowire` for `src/*`.
 *
 * @internal
 */
final class HookRunnersAutoconfigurationTest extends TestCase
{
	/**
	 */
	public function testHookInterfacesAreAutoconfiguredAndWiredIntoHookRunners () : void
	{
		$container = new ContainerBuilder();

		// Registers the hook interfaces themselves as autoconfigured abstract definitions,
		// the same way the bundle's `config/services.yaml` does for every class/interface found
		// via its `src/*` resource glob. This is what makes the container read the interfaces'
		// #[AutoconfigureTag] attribute and apply it to any class implementing them.
		foreach (
			[
				BuildHookInterface::class,
				DeployContainerHookInterface::class,
				DeployAppHookInterface::class,
				DeployHookInterface::class,
			] as $interface
		)
		{
			$container->register($interface)
				->setAbstract(true)
				->setAutoconfigured(true)
				->addTag("container.excluded");
		}

		foreach (
			[
				FixtureBuildHook::class,
				FixtureDeployContainerHook::class,
				FixtureDeployAppHook::class,
				FixtureLegacyDeployHook::class,
			] as $fixtureClass
		)
		{
			$container->register($fixtureClass)
				->setAutoconfigured(true)
				->setPublic(true);
		}

		$container->register(HookRunners::class)
			->setAutowired(true)
			->setPublic(true);

		$container->compile();

		// the interfaces' #[AutoconfigureTag] attribute must tag the fixtures correctly
		self::assertSame(
			[FixtureBuildHook::class],
			array_keys($container->findTaggedServiceIds(HookRunners::TAG_BUILD_HOOK)),
		);
		self::assertSame(
			[FixtureDeployAppHook::class],
			array_keys($container->findTaggedServiceIds(HookRunners::TAG_DEPLOY_APP_HOOK)),
		);
		self::assertEqualsCanonicalizing(
			[FixtureDeployContainerHook::class, FixtureLegacyDeployHook::class],
			array_keys($container->findTaggedServiceIds(HookRunners::TAG_DEPLOY_CONTAINER_HOOK)),
		);

		/** @var HookRunners $runners */
		$runners = $container->get(HookRunners::class);

		// the #[AutowireIterator] on HookRunners' constructor must actually receive those tagged services
		self::assertSame(
			[$container->get(FixtureBuildHook::class)],
			$this->readHookRunnersProperty($runners, "buildHooks"),
		);
		self::assertSame(
			[$container->get(FixtureDeployAppHook::class)],
			$this->readHookRunnersProperty($runners, "deployAppHooks"),
		);
		self::assertEqualsCanonicalizing(
			[
				$container->get(FixtureDeployContainerHook::class),
				$container->get(FixtureLegacyDeployHook::class),
			],
			$this->readHookRunnersProperty($runners, "deployContainerHooks"),
		);
	}

	/**
	 * @return array<object>
	 */
	private function readHookRunnersProperty (HookRunners $runners, string $property) : array
	{
		$value = new \ReflectionProperty(HookRunners::class, $property)
			->getValue($runners);
		$items = \is_array($value) ? $value : iterator_to_array($value, false);

		return array_map(
			static function (mixed $item) : object
			{
				self::assertIsObject($item);

				return $item;
			},
			$items,
		);
	}
}