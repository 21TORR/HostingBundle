<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment;

use Symfony\Component\Console\Style\SymfonyStyle;

final class TaskRunners
{
	/** @var PostBuildTaskInterface[] */
	private iterable $postBuildTasks;
	/** @var PostDeploymentTaskInterface[] */
	private iterable $postDeploymentTasks;

	/**
	 */
	public function __construct (
		iterable $postBuildTasks,
		iterable $postDeploymentTasks,
	)
	{
		$this->postBuildTasks = $postBuildTasks;
		$this->postDeploymentTasks = $postDeploymentTasks;
	}


	/**
	 */
	public function runPostBuild (SymfonyStyle $io) : void
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
	public function runPostDeployment (SymfonyStyle $io) : void
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
