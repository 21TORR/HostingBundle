<?php declare(strict_types=1);

namespace Torr\Hosting\Hosting;

use Torr\Hosting\Tier\HostingTier;

final readonly class HostingEnvironment
{
	private HostingTier $tier;

	/**
	 */
	public function __construct (
		string|HostingTier $tier,
		private ?string $installationKey = null,
	)
	{
		$this->tier = $this->getHostingTier($tier);
	}

	/**
	 * @todo refactor in v3
	 */
	private function getHostingTier (string|HostingTier $tier) : HostingTier
	{
		if ($tier instanceof HostingTier)
		{
			return $tier;
		}

		if ("live" === $tier)
		{
			\trigger_deprecation("21torr/hosting", "2.1.0", "The hosting tier 'live' is deprecated. Use 'production' instead.");
			return HostingTier::PRODUCTION;
		}

		return HostingTier::from($tier);
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
	 * @deprecated use {@see self::isProduction()} instead.
	 *
	 * @todo remove in v3
	 */
	public function isLive () : bool
	{
		\trigger_deprecation("21torr/hosting", "2.1.0", "The hosting tier 'live' is deprecated. Use 'production' instead.");
		return $this->isProduction();
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
	 * @deprecated
	 *
	 * @todo remove in v3
	 */
	public function getInstallationKey () : string
	{
		\trigger_deprecation("21torr/hosting", "2.1.0", "The installation key is deprecated.");
		return $this->installationKey ?? "n/a";
	}
}
