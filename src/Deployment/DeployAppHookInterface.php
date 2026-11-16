<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Torr\Hosting\Deployment\Exception\HookFailedException;

#[AutoconfigureTag(HookRunners::TAG_DEPLOY_APP_HOOK)]
interface DeployAppHookInterface
{
	/**
	 * Returns the label of the hook
	 */
	public function getLabel () : string;

	/**
	 * Runs the app deploy hook
	 *
	 * @throws HookFailedException if the hook failed
	 */
	public function runDeployApp (TaskCli $io) : void;
}
