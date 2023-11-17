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
		private readonly ?string $installationKey = null,
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

	/**
	 * @deprecated
	 */
	public function getInstallationKey () : string
	{
		\trigger_deprecation("21torr/hosting", "2.1.0", "The installation key is deprecated.");
		return $this->installationKey ?? "n/a";
	}
}
