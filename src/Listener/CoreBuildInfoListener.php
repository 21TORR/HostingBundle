<?php declare(strict_types=1);

namespace Torr\Hosting\Listener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Torr\Hosting\Event\CollectBuildInfoEvent;

final class CoreBuildInfoListener
{

	/**
	 */
	#[AsEventListener(CollectBuildInfoEvent::class)]
	public function onCollectBuildInfo (CollectBuildInfoEvent $event) : void
	{
		$event->set("built", (new \DateTimeImmutable())->format("c"));
	}
}
