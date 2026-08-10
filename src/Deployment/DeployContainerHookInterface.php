<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(HookRunners::TAG_DEPLOY_CONTAINER_HOOK)]
interface DeployContainerHookInterface
{
	/**
	 * Returns the label of the hook
	 */
	public function getLabel () : string;

	/**
	 * Runs the post container deployment hook
	 */
	public function runDeployContainer (TaskCli $io) : void;
}
