<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(HookRunners::TAG_DEPLOY_HOOK)]
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
