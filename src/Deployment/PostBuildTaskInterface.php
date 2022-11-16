<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment;

interface PostBuildTaskInterface
{
	/**
	 * Returns the label of the run
	 */
	public function getLabel () : string;

	/**
	 * Runs the post build tasks
	 */
	public function runPostBuild (TaskCli $io) : void;
}
