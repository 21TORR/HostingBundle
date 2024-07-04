<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment;

interface DeployHookInterface
{
	/**
	 * Returns the label of the hook
	 */
	public function getLabel () : string;

	/**
	 * Runs the post deployment hook
	 */
	public function runPostDeployment (TaskCli $io) : void;
}
