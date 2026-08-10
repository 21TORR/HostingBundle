<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class HookRunners
{
	public const string TAG_BUILD_HOOK = "hosting.hook.build";
	public const string TAG_DEPLOY_CONTAINER_HOOK = "hosting.hook.deploy-container";
	public const string TAG_DEPLOY_APP_HOOK = "hosting.hook.deploy-app";

	/**
	 */
	public function __construct (
		/** @var BuildHookInterface[] */
		#[AutowireIterator(tag: self::TAG_BUILD_HOOK)]
		private iterable $buildHooks,
		/** @var list<DeployContainerHookInterface|DeployHookInterface> */
		#[AutowireIterator(tag: self::TAG_DEPLOY_CONTAINER_HOOK)]
		private iterable $deployContainerHooks,
		/** @var DeployAppHookInterface[] */
		#[AutowireIterator(tag: self::TAG_DEPLOY_APP_HOOK)]
		private iterable $deployAppHooks,
	) {}

	/**
	 */
	public function runBuildHooks (TaskCli $io) : void
	{
		$first = true;

		foreach ($this->buildHooks as $runner)
		{
			if ($first)
			{
				$first = false;
			}
			else
			{
				$io->newLine(2);
			}

			$io->section("Run Build Hook: <fg=magenta>{$runner->getLabel()}</>");
			$runner->runPostBuild($io);
		}
	}

	/**
	 */
	public function runDeployContainerHooks (TaskCli $io) : void
	{
		$first = true;

		foreach ($this->deployContainerHooks as $runner)
		{
			if ($first)
			{
				$first = false;
			}
			else
			{
				$io->newLine(2);
			}

			$io->section("Run Deploy Container Hook: <fg=magenta>{$runner->getLabel()}</>");

			if ($runner instanceof DeployHookInterface)
			{
				trigger_deprecation(
					"21torr/hosting",
					"4.2.1",
					\sprintf(
						"Using 'deploy hooks' is deprecated, use 'DeployContainerHook' instead. Used in '%s'",
						$runner::class,
					),
				);

				$runner->runPostDeployment($io);
				continue;
			}

			$runner->runDeployContainer($io);
		}
	}

	/**
	 */
	public function runDeployAppHooks (TaskCli $io) : void
	{
		$first = true;

		foreach ($this->deployAppHooks as $runner)
		{
			if ($first)
			{
				$first = false;
			}
			else
			{
				$io->newLine(2);
			}

			$io->section("Run Deploy App Hook: <fg=magenta>{$runner->getLabel()}</>");
			$runner->runDeployApp($io);
		}
	}
}
