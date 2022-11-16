<?php declare(strict_types=1);

namespace Torr\Hosting\Hosting;

use Torr\Hosting\Tier\HostingTier;

final class HostingEnvironment
{
	private readonly HostingTier $tier;

	/**
	 */
	public function __construct (
		string $tier,
	)
	{
		$this->tier = HostingTier::from($tier);
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
	public function isLive () : bool
	{
		return HostingTier::LIVE === $this->tier;
	}

	/**
	 */
	public function getTier () : HostingTier
	{
		return $this->tier;
	}
}
