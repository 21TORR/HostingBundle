<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(HookRunners::TAG_BUILD_HOOK)]
interface BuildHookInterface
{
	/**
	 * Returns the label of the hook
	 */
	public function getLabel () : string;

	/**
	 * Runs the build hook
	 */
	public function runPostBuild (TaskCli $io) : void;
}
