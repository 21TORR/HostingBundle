<?php declare(strict_types=1);

namespace Tests\Torr\Hosting\BuildInfo;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Torr\Hosting\Event\CollectBuildInfoEvent;
use Torr\Hosting\Git\GitVersionFetcher;
use Torr\Hosting\Listener\CoreBuildInfoListener;
use Torr\Hosting\Listener\GitBuildInfoListener;
use Torr\Hosting\Upsun\UpsunVersionFetcher;

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
		yield "null" => [null, null, []];
		yield "set" => [[
			"commit" => "c",
			"last-tag" => "t",
			"tree-id" => "tree",
		], null, [
			"git.commit" => "c",
			"git.last-tag" => "t",
			"git.tree-id" => "tree",
		]];
		yield "upsun fallback" => [null, [
			"tree-id" => "tree",
		], [
			"git.tree-id" => "tree",
		]];
		yield "git wins over upsun" => [[
			"commit" => "c",
			"last-tag" => null,
			"tree-id" => "tree",
		], [
			"tree-id" => "other tree",
		], [
			"git.commit" => "c",
			"git.last-tag" => null,
			"git.tree-id" => "tree",
		]];
	}

	/**
	 */
	#[DataProvider("provideBuildInfoGitIntegration")]
	public function testBuildInfoGitIntegration (?array $data, ?array $upsunData, array $expected) : void
	{
		$versionFetcher = $this->createMock(GitVersionFetcher::class);

		$versionFetcher
			->expects(self::once())
			->method("detectVersion")
			->willReturn($data);

		$upsunVersionFetcher = self::createStub(UpsunVersionFetcher::class);

		$upsunVersionFetcher
			->method("detectVersion")
			->willReturn($upsunData);

		$listener = new GitBuildInfoListener($versionFetcher, $upsunVersionFetcher);

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
