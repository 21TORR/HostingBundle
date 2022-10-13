<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment;

use Symfony\Component\Console\Style\SymfonyStyle;

interface PostDeploymentTaskInterface
{
	/**
	 * Returns the label of the run
	 */
	public function getLabel () : string;

	/**
	 * Runs the post deployment tasks
	 */
	public function runPostDeployment (SymfonyStyle $io) : void;
}
