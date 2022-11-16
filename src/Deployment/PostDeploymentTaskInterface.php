<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment;

interface PostDeploymentTaskInterface
{
	/**
	 * Returns the label of the run
	 */
	public function getLabel () : string;

	/**
	 * Runs the post deployment tasks
	 */
	public function runPostDeployment (TaskCli $io) : void;
}
