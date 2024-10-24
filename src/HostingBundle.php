<?php declare(strict_types=1);

namespace Torr\Hosting;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Torr\BundleHelpers\Bundle\ConfigurableBundleExtension;
use Torr\Hosting\DependencyInjection\HostingBundleConfiguration;
use Torr\Hosting\Deployment\BuildHookInterface;
use Torr\Hosting\Deployment\DeployHookInterface;
use Torr\Hosting\Deployment\HookRunners;
use Torr\Hosting\Hosting\HostingEnvironment;

final class HostingBundle extends Bundle
{
	/**
	 */
	#[\Override]
	public function getContainerExtension() : ExtensionInterface
	{
		return new ConfigurableBundleExtension(
			$this,
			new HostingBundleConfiguration(),
			static function (array $config, ContainerBuilder $container) : void
			{
				$container->getDefinition(HostingEnvironment::class)
					->setArgument('$tier', $config["tier"])
					->setArgument('$installationKey', $config["installation"]);
			},
			"21torr_hosting",
		);
	}

	/**
	 */
	#[\Override]
	public function build(ContainerBuilder $container) : void
	{
		$container->registerForAutoconfiguration(BuildHookInterface::class)
			->addTag(HookRunners::TAG_BUILD_HOOK);

		$container->registerForAutoconfiguration(DeployHookInterface::class)
			->addTag(HookRunners::TAG_DEPLOY_HOOK);
	}

	/**
	 */
	#[\Override]
	public function getPath() : string
	{
		return \dirname(__DIR__);
	}
}
