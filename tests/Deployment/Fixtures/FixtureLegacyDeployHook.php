<?php declare(strict_types=1);

namespace Tests\Torr\Hosting\Deployment\Fixtures;

use Torr\Hosting\Deployment\DeployHookInterface;
use Torr\Hosting\Deployment\TaskCli;

/**
 * Implements the deprecated {@see DeployHookInterface} on purpose, to verify that the
 * BC layer is still autoconfigured/wired correctly.
 *
 * @internal
 */
final class FixtureLegacyDeployHook implements DeployHookInterface
{
	#[\Override]
	public function getLabel () : string
	{
		return "fixture legacy deploy hook";
	}

	#[\Override]
	public function runPostDeployment (TaskCli $io) : void
	{
		// intentionally left blank, only used to verify DI wiring
	}
}
