<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(HookRunners::TAG_INIT_HOOK)]
interface InitHookInterface
{
	/**
	 * Returns the label of the hook
	 */
	public function getLabel () : string;

	/**
	 * Runs the app initialization hook
	 */
	public function runInit (TaskCli $io) : void;
}
