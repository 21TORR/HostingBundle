<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class HookRunners
{
	public const string TAG_BUILD_HOOK = "hosting.hook.build";
	public const string TAG_DEPLOY_HOOK = "hosting.hook.deploy";
	public const string TAG_INIT_HOOK = "hosting.hook.init";

	/**
	 */
	public function __construct (
		/** @var BuildHookInterface[] */
		#[AutowireIterator(tag: self::TAG_BUILD_HOOK)]
		private iterable $buildHooks,
		/** @var DeployHookInterface[] */
		#[AutowireIterator(tag: self::TAG_DEPLOY_HOOK)]
		private iterable $deployHooks,
		/** @var InitHookInterface[] */
		#[AutowireIterator(tag: self::TAG_INIT_HOOK)]
		private iterable $initHooks,
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
	public function runDeployHooks (TaskCli $io) : void
	{
		$first = true;

		foreach ($this->deployHooks as $runner)
		{
			if ($first)
			{
				$first = false;
			}
			else
			{
				$io->newLine(2);
			}

			$io->section("Run Deploy Hook: <fg=magenta>{$runner->getLabel()}</>");
			$runner->runPostDeployment($io);
		}
	}

	/**
	 */
	public function runInitHooks (TaskCli $io) : void
	{
		$first = true;

		foreach ($this->initHooks as $runner)
		{
			if ($first)
			{
				$first = false;
			}
			else
			{
				$io->newLine(2);
			}

			$io->section("Run Init Hook: <fg=magenta>{$runner->getLabel()}</>");
			$runner->runInit($io);
		}
	}
}
