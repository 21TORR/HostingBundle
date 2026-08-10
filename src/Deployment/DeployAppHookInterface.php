<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(HookRunners::TAG_DEPLOY_APP_HOOK)]
interface DeployAppHookInterface
{
	/**
	 * Returns the label of the hook
	 */
	public function getLabel () : string;

	/**
	 * Runs the app deploy hook
	 */
	public function runDeployApp (TaskCli $io) : void;
}
