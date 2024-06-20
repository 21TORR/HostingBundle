<?php declare(strict_types=1);

namespace Tests\Torr\Hosting\BuildInfo;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Torr\Hosting\Event\CollectBuildInfoEvent;
use Torr\Hosting\Git\GitVersionFetcher;
use Torr\Hosting\Listener\CoreBuildInfoListener;
use Torr\Hosting\Listener\GitBuildInfoListener;

/**
 * @internal
 */
final class BuildInfoTest extends TestCase
{
	/**
	 *
	 */
	public static function provideBuildInfoGitIntegration () : iterable
	{
		yield "null" => [null, []];
		yield "set" => [[
			"commit" => "c",
			"last-tag" => "t",
		], [
			"git.commit" => "c",
			"git.last-tag" => "t",
		]];
	}

	/**
	 * @dataProvider provideBuildInfoGitIntegration
	 */
	public function testBuildInfoGitIntegration (?array $data, array $expected) : void
	{
		$versionFetcher = $this->createMock(GitVersionFetcher::class);

		$versionFetcher
			->expects(self::once())
			->method("detectVersion")
			->willReturn($data);

		$listener = new GitBuildInfoListener($versionFetcher);

		$dispatcher = new EventDispatcher();
		$dispatcher->addListener(
			CollectBuildInfoEvent::class,
			$listener->onCollectBuildInfo(...),
		);

		$event = new CollectBuildInfoEvent();
		$dispatcher->dispatch($event);

		self::assertEqualsCanonicalizing($expected, $event->getInfo());
	}

	/**
	 */
	public function testBuildDate () : void
	{
		$clock = new MockClock(new \DateTimeImmutable('2023-12-24'));
		$listener = new CoreBuildInfoListener($clock);

		$dispatcher = new EventDispatcher();
		$dispatcher->addListener(
			CollectBuildInfoEvent::class,
			$listener->onCollectBuildInfo(...),
		);

		$event = new CollectBuildInfoEvent();
		$dispatcher->dispatch($event);

		self::assertEqualsCanonicalizing([
			"built" => $clock->now()->format("c"),
		], $event->getInfo());
	}
}
