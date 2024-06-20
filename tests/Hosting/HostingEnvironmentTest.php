<?php declare(strict_types=1);

namespace Tests\Torr\Hosting\Hosting;

use PHPUnit\Framework\TestCase;
use Torr\Hosting\Hosting\HostingEnvironment;
use Torr\Hosting\Tier\HostingTier;

/**
 * @internal
 */
final class HostingEnvironmentTest extends TestCase
{
	/**
	 */
	public function testInstallationKey () : void
	{
		$environment = new HostingEnvironment(HostingTier::PRODUCTION, "installation");

		self::assertSame("installation", $environment->getInstallationKey());
	}

	/**
	 */
	public function testHostingTierConstructor () : void
	{
		$environment = new HostingEnvironment(HostingTier::PRODUCTION, "installation");
		self::assertSame(HostingTier::PRODUCTION, $environment->getTier());

		$environment2 = new HostingEnvironment("staging", "installation");
		self::assertSame(HostingTier::STAGING, $environment2->getTier());
	}

	/**
	 *
	 */
	public static function provideHostingTiers () : iterable
	{
		yield ["development", HostingTier::DEVELOPMENT];
		yield ["staging", HostingTier::STAGING];
		yield ["production", HostingTier::PRODUCTION];
		yield [HostingTier::DEVELOPMENT, HostingTier::DEVELOPMENT];
		yield [HostingTier::STAGING, HostingTier::STAGING];
		yield [HostingTier::PRODUCTION, HostingTier::PRODUCTION];
	}

	/**
	 * @dataProvider provideHostingTiers
	 */
	public function testHostingTiers (string|HostingTier $value, HostingTier $expected) : void
	{
		$environment = new HostingEnvironment($value, "installation");
		self::assertSame($expected, $environment->getTier());
	}
}
