<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment\Task;

use Torr\Hosting\BuildInfo\BuildInfoStorage;
use Torr\Hosting\Deployment\BuildHookInterface;
use Torr\Hosting\Deployment\TaskCli;

/**
 * Refreshes the build info. Should run as first task.
 */
final readonly class ResetBuildInfoTask implements BuildHookInterface
{
	/**
	 */
	public function __construct (
		private BuildInfoStorage $buildInfo,
	) {}

	/**
	 */
	public function getLabel () : string
	{
		return "Reset Build Info";
	}

	/**
	 */
	public function runPostBuild (TaskCli $io) : void
	{
		$this->buildInfo->refresh();
		$io->done("Build info reset");
	}
}
