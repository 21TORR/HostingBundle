<?php declare(strict_types=1);

namespace Tests\Torr\Hosting\Deployment\Fixtures;

use Torr\Hosting\Deployment\BuildHookInterface;
use Torr\Hosting\Deployment\TaskCli;

/**
 * @internal
 */
final class FixtureBuildHook implements BuildHookInterface
{
	#[\Override]
	public function getLabel () : string
	{
		return "fixture build hook";
	}

	#[\Override]
	public function runPostBuild (TaskCli $io) : void
	{
		// intentionally left blank, only used to verify DI wiring
	}
}