<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final class TaskRunners
{
	public const TAG_POST_BUILD = "hosting.task.post-build";
	public const TAG_POST_DEPLOYMENT = "hosting.task.post-deploy";

	/**
	 */
	public function __construct (
		/** @var PostBuildTaskInterface[] */
		#[AutowireIterator(tag: self::TAG_POST_BUILD)]
		private iterable $postBuildTasks,
		/** @var PostDeploymentTaskInterface[] */
		#[AutowireIterator(tag: self::TAG_POST_DEPLOYMENT)]
		private iterable $postDeploymentTasks,
	) {}

	/**
	 */
	public function runPostBuild (TaskCli $io) : void
	{
		$first = true;

		foreach ($this->postBuildTasks as $runner)
		{
			if ($first)
			{
				$first = false;
			}
			else
			{
				$io->newLine(2);
			}

			$io->section("Run Post Build Step: <fg=magenta>{$runner->getLabel()}</>");
			$runner->runPostBuild($io);
		}
	}

	/**
	 */
	public function runPostDeployment (TaskCli $io) : void
	{
		$first = true;

		foreach ($this->postDeploymentTasks as $runner)
		{
			if ($first)
			{
				$first = false;
			}
			else
			{
				$io->newLine(2);
			}

			$io->section("Run Post Deployment Step: <fg=magenta>{$runner->getLabel()}</>");
			$runner->runPostDeployment($io);
		}
	}
}
