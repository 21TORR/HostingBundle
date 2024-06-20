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
		private string $installationKey,
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
			// @phpstan-ignore-next-line todoBy.sfDeprecation
			trigger_deprecation("21torr/hosting", "2.1.0", "The hosting tier 'live' is deprecated. Use 'production' instead.");

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
	 * @deprecated use {@see self::isProduction()} instead
	 *
	 * @todo remove in v3
	 */
	public function isLive () : bool
	{
		// @phpstan-ignore-next-line todoBy.sfDeprecation
		trigger_deprecation("21torr/hosting", "2.1.0", "The hosting tier 'live' is deprecated. Use 'production' instead.");

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
	 */
	public function getInstallationKey () : string
	{
		return $this->installationKey;
	}
}
