<?php declare(strict_types=1);

namespace Tests\Torr\Hosting\Hosting;

use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Torr\Hosting\Hosting\HostingEnvironment;
use PHPUnit\Framework\TestCase;
use Torr\Hosting\Tier\HostingTier;

class HostingEnvironmentTest extends TestCase
{
	use ExpectDeprecationTrait;

	/**
	 * @group legacy
	 */
	public function testLegacyInstallationKey () : void
	{
		$this->expectDeprecation("Since 21torr/hosting 2.1.0: The installation key is deprecated.");
		$environment = new HostingEnvironment(HostingTier::LIVE, "installation");

		self::assertSame("installation", $environment->getInstallationKey());
	}

	/**
	 * @group legacy
	 */
	public function testLegacyDefaultInstallationKey () : void
	{
		$this->expectDeprecation("Since 21torr/hosting 2.1.0: The installation key is deprecated.");
		$environment = new HostingEnvironment(HostingTier::LIVE);

		self::assertSame("n/a", $environment->getInstallationKey());
	}

	/**
	 */
	public function testHostingTierConstructor () : void
	{
		$environment = new HostingEnvironment(HostingTier::LIVE);
		self::assertSame(HostingTier::LIVE, $environment->getTier());

		$environment2 = new HostingEnvironment("staging");
		self::assertSame(HostingTier::STAGING, $environment2->getTier());
	}
}
