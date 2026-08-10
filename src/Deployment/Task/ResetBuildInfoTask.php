<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment\Task;

use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Torr\Hosting\BuildInfo\BuildInfoStorage;
use Torr\Hosting\Deployment\BuildHookInterface;
use Torr\Hosting\Deployment\TaskCli;

/**
 * Refreshes the build info. Should run as the first task.
 */
#[AsTaggedItem(priority: 10000)]
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
		$io->done("build info stored");
	}
}
