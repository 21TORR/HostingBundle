<?php declare(strict_types=1);

namespace Torr\Hosting\Listener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Torr\Hosting\Event\ValidateAppEvent;
use Torr\Hosting\Hosting\HostingEnvironment;
use Torr\Hosting\Tier\HostingTier;

/**
 * @final
 */
readonly class ValidateAppListener
{
	public function __construct (
		private HostingEnvironment $environment,
	) {}

	#[AsEventListener]
	public function onValidateApp (ValidateAppEvent $event) : void
	{
		// Technically this can't happen, as we can't set anything besides the
		// correct type as tier. However we want to force parsing of the hosting tier,
		// so that we catch invalid tiers.
		// @phpstan-ignore-next-line instanceof.alwaysTrue (this is a dummy check, please read above for the explanation)
		if (!$this->environment->getTier() instanceof HostingTier)
		{
			$event->markAppAsInvalid("Invalid Hosting Tier");
		}
	}
}
