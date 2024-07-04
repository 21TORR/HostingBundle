<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class HookRunners
{
	public const string TAG_BUILD = "hosting.hook.build";
	public const string TAG_DEPLOY = "hosting.hook.deploy";

	/**
	 */
	public function __construct (
		/** @var PostBuildTaskInterface[] */
		#[AutowireIterator(tag: self::TAG_BUILD)]
		private iterable $buildHooks,
		/** @var PostDeploymentTaskInterface[] */
		#[AutowireIterator(tag: self::TAG_DEPLOY)]
		private iterable $deployHooks,
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
}
