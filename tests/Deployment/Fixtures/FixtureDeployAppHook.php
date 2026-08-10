<?php declare(strict_types=1);

namespace Tests\Torr\Hosting\Deployment\Fixtures;

use Torr\Hosting\Deployment\DeployAppHookInterface;
use Torr\Hosting\Deployment\TaskCli;

/**
 * @internal
 */
final class FixtureDeployAppHook implements DeployAppHookInterface
{
	#[\Override]
	public function getLabel () : string
	{
		return "fixture deploy app hook";
	}

	#[\Override]
	public function runDeployApp (TaskCli $io) : void
	{
		// intentionally left blank, only used to verify DI wiring
	}
}
