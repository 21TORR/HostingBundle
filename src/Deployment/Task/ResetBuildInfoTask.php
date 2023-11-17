<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment\Task;

use Torr\Hosting\BuildInfo\BuildInfo;
use Torr\Hosting\Deployment\PostBuildTaskInterface;
use Torr\Hosting\Deployment\TaskCli;

final readonly class ResetBuildInfoTask implements PostBuildTaskInterface
{
	/**
	 */
	public function __construct (
		private BuildInfo $buildInfo,
	) {}

	/**
	 * @inheritDoc
	 */
	public function getLabel () : string
	{
		return "Reset Build Info";
	}

	/**
	 * @inheritDoc
	 */
	public function runPostBuild (TaskCli $io) : void
	{
		$this->buildInfo->reset();
		$io->done("Build info reset");
	}
}
