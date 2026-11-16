<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Torr\Hosting\Deployment\Exception\HookFailedException;

#[AutoconfigureTag(HookRunners::TAG_BUILD_HOOK)]
interface BuildHookInterface
{
	/**
	 * Returns the label of the hook
	 */
	public function getLabel () : string;

	/**
	 * Runs the build hook
	 *
	 * @throws HookFailedException if the hook failed
	 */
	public function runPostBuild (TaskCli $io) : void;
}
