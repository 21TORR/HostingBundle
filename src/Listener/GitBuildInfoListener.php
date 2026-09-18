<?php declare(strict_types=1);

namespace Torr\Hosting\Listener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Torr\Hosting\Event\CollectBuildInfoEvent;
use Torr\Hosting\Git\GitVersionFetcher;
use Torr\Hosting\Upsun\UpsunVersionFetcher;

final readonly class GitBuildInfoListener
{
	public function __construct (
		private GitVersionFetcher $localGitVersionFetcher,
		private UpsunVersionFetcher $upsunVersionFetcher,
	) {}

	/**
	 * Collects the version of the sources, either from the local git repository or — if there is none to
	 * read from — from the environment of an Upsun build.
	 */
	#[AsEventListener(CollectBuildInfoEvent::class)]
	public function onCollectBuildInfo (CollectBuildInfoEvent $event) : void
	{
		$versionData = $this->localGitVersionFetcher->detectVersion()
			?? $this->upsunVersionFetcher->detectVersion();

		if (null !== $versionData)
		{
			foreach ($versionData as $key => $value)
			{
				$event->set("git.{$key}", $value);
			}
		}
	}
}
