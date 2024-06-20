<?php declare(strict_types=1);

namespace Tests\Torr\Hosting\Hosting;

use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\Config\Definition\Processor;
use Torr\Hosting\DependencyInjection\HostingBundleConfiguration;
use Torr\Hosting\Hosting\HostingEnvironment;
use Torr\Hosting\Tier\HostingTier;

/**
 * @internal
 */
final class HostingEnvironmentTest extends TestCase
{
	use ExpectDeprecationTrait;

	/**
	 */
	public function testInstallationKey () : void
	{
		$environment = new HostingEnvironment(HostingTier::LIVE, "installation");

		self::assertSame("installation", $environment->getInstallationKey());
	}

	/**
	 */
	public function testHostingTierConstructor () : void
	{
		$environment = new HostingEnvironment(HostingTier::LIVE, "installation");
		self::assertSame(HostingTier::LIVE, $environment->getTier());

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

	/**
	 * @group legacy
	 */
	public function testLegacyLiveHostingTier () : void
	{
		$this->expectDeprecation("Since 21torr/hosting 2.1.0: The hosting tier 'live' is deprecated. Use 'production' instead.");
		$environment = new HostingEnvironment("live", "installation");
		self::assertSame(HostingTier::PRODUCTION, $environment->getTier());
	}

	/**
	 * @group legacy
	 */
	public function testLegacyLiveHostingTierGetter () : void
	{
		$this->expectDeprecation("Since 21torr/hosting 2.1.0: The hosting tier 'live' is deprecated. Use 'production' instead.");
		$environment = new HostingEnvironment(HostingTier::PRODUCTION, "installation");
		self::assertTrue($environment->isLive());
	}

	/**
	 * @group legacy
	 */
	public function testLegacyConfigValues () : void
	{
		$config = new HostingBundleConfiguration();
		$configProcessor = new Processor();
		$resolved = $configProcessor->processConfiguration($config, [[
			"tier" => "live",
		]]);

		// live is explicitly allowed
		self::assertSame("live", $resolved["tier"]);
	}
}
