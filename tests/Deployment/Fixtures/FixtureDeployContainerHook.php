<?php declare(strict_types=1);

namespace Tests\Torr\Hosting\Deployment\Fixtures;

use Torr\Hosting\Deployment\DeployContainerHookInterface;
use Torr\Hosting\Deployment\TaskCli;

/**
 * @internal
 */
final class FixtureDeployContainerHook implements DeployContainerHookInterface
{
	#[\Override]
	public function getLabel () : string
	{
		return "fixture deploy container hook";
	}

	#[\Override]
	public function runDeployContainer (TaskCli $io) : void
	{
		// intentionally left blank, only used to verify DI wiring
	}
}