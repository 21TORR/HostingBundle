<?php declare(strict_types=1);

namespace Tests\Torr\Hosting\Upsun;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Torr\Hosting\Upsun\UpsunVersionFetcher;

/**
 * @internal
 */
final class UpsunVersionFetcherTest extends TestCase
{
	/**
	 */
	public function testDetectsTreeId () : void
	{
		self::assertSame(
			["tree-id" => "6ca2b082c4982a05d9978c0e48bfbae57de44389"],
			new UpsunVersionFetcher("6ca2b082c4982a05d9978c0e48bfbae57de44389")->detectVersion(),
		);
	}

	/**
	 * The env var is missing outside of Upsun, which the `default::` fallback resolves to `null`.
	 */
	public function testIgnoresMissingTreeId () : void
	{
		self::assertNull(new UpsunVersionFetcher(null)->detectVersion());
	}

	/**
	 */
	public function testIgnoresEmptyTreeId () : void
	{
		self::assertNull(new UpsunVersionFetcher("")->detectVersion());
	}

	/**
	 * The tree id is injected from the environment. Resolving it must not fail if the env var is missing,
	 * which is the regular case outside of Upsun.
	 */
	public function testEnvVariableIsWiredUp () : void
	{
		unset($_ENV["PLATFORM_TREE_ID"], $_SERVER["PLATFORM_TREE_ID"]);

		self::assertNull($this->fetchFromContainer()->detectVersion());

		$_ENV["PLATFORM_TREE_ID"] = "6ca2b082c4982a05d9978c0e48bfbae57de44389";

		try
		{
			self::assertSame(
				["tree-id" => "6ca2b082c4982a05d9978c0e48bfbae57de44389"],
				$this->fetchFromContainer()->detectVersion(),
			);
		}
		finally
		{
			unset($_ENV["PLATFORM_TREE_ID"]);
		}
	}

	/**
	 * Builds the fetcher the way an app does: autowired, with the env var resolved by the container.
	 *
	 * A dumped container resolves its env placeholders while running, a `ContainerBuilder` only if it is
	 * compiled with `$resolveEnvPlaceholders`.
	 */
	private function fetchFromContainer () : UpsunVersionFetcher
	{
		$container = new ContainerBuilder();
		$container->register(UpsunVersionFetcher::class)
			->setAutowired(true)
			->setPublic(true);
		$container->compile(true);

		$fetcher = $container->get(UpsunVersionFetcher::class);
		\assert($fetcher instanceof UpsunVersionFetcher);

		return $fetcher;
	}
}
