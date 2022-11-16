<?php declare(strict_types=1);

namespace Torr\Hosting;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Torr\BundleHelpers\Bundle\ConfigurableBundleExtension;
use Torr\Hosting\DependencyInjection\HostingBundleConfiguration;
use Torr\Hosting\Deployment\PostBuildTaskInterface;
use Torr\Hosting\Deployment\PostDeploymentTaskInterface;
use Torr\Hosting\Hosting\HostingEnvironment;

final class HostingBundle extends Bundle
{
	/**
	 * @inheritDoc
	 */
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
		);
	}

	/**
	 * @inheritDoc
	 */
	public function build(ContainerBuilder $container) : void
	{
		$container->registerForAutoconfiguration(PostBuildTaskInterface::class)
			->addTag("hosting.task.post-build");

		$container->registerForAutoconfiguration(PostDeploymentTaskInterface::class)
			->addTag("hosting.task.post-deploy");
	}


	/**
	 * @inheritDoc
	 */
	public function getPath() : string
	{
		return \dirname(__DIR__);
	}
}
