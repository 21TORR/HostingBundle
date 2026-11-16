<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Torr\Hosting\Deployment\Exception\HookFailedException;

/**
 * @deprecated Migrate to {@see DeployContainerHookInterface}
 */
#[AutoconfigureTag(HookRunners::TAG_DEPLOY_CONTAINER_HOOK)]
interface DeployHookInterface
{
	/**
	 * Returns the label of the hook
	 */
	public function getLabel () : string;

	/**
	 * Runs the post (container) deployment hook
	 *
	 * @throws HookFailedException if the hook failed
	 */
	public function runPostDeployment (TaskCli $io) : void;
}
