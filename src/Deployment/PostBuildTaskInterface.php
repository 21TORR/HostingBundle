<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment;

use Symfony\Component\Console\Style\SymfonyStyle;

interface PostBuildTaskInterface
{
	/**
	 * Returns the label of the run
	 */
	public function getLabel () : string;

	/**
	 * Runs the post build tasks
	 */
	public function runPostBuild (SymfonyStyle $io) : void;
}
