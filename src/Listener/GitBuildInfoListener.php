<?php declare(strict_types=1);

namespace Torr\Hosting\Listener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Torr\Hosting\Event\CollectBuildInfoEvent;
use Torr\Hosting\Git\GitVersionFetcher;

final readonly class GitBuildInfoListener
{
	public function __construct (
		private GitVersionFetcher $localGitVersionFetcher,
	) {}

	/**
	 */
	#[AsEventListener(CollectBuildInfoEvent::class)]
	public function onCollectBuildInfo (CollectBuildInfoEvent $event) : void
	{
		$gitData = $this->localGitVersionFetcher->detectVersion();

		if (null !== $gitData)
		{
			foreach ($gitData as $key => $value)
			{
				$event->set("git.{$key}", $value);
			}
		}
	}
}
