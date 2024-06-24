<?php declare(strict_types=1);

namespace Torr\Hosting\Hosting;

use Torr\Hosting\Tier\HostingTier;

final readonly class HostingEnvironment
{
	private HostingTier $tier;

	/**
	 */
	public function __construct (
		private string $installationKey,
		HostingTier|string $tier,
	)
	{
		$this->tier = is_string($tier)
			? HostingTier::from($tier)
			: $tier;
	}

	/**
	 */
	public function isDevelopment () : bool
	{
		return HostingTier::DEVELOPMENT === $this->tier;
	}

	/**
	 */
	public function isStaging () : bool
	{
		return HostingTier::STAGING === $this->tier;
	}

	/**
	 */
	public function isProduction () : bool
	{
		return HostingTier::PRODUCTION === $this->tier;
	}

	/**
	 */
	public function getTier () : HostingTier
	{
		return $this->tier;
	}

	/**
	 */
	public function getInstallationKey () : string
	{
		return $this->installationKey;
	}
}
