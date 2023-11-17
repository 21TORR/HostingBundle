<?php declare(strict_types=1);

namespace Torr\Hosting\Listener;

use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Torr\Hosting\Event\CollectBuildInfoEvent;

final readonly class CoreBuildInfoListener
{
	public function __construct (
		private ClockInterface $clock,
	) {}


	/**
	 */
	#[AsEventListener(CollectBuildInfoEvent::class)]
	public function onCollectBuildInfo (CollectBuildInfoEvent $event) : void
	{
		$event->set("built", $this->clock->now()->format("c"));
	}
}
