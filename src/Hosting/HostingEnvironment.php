<?php declare(strict_types=1);

namespace Torr\Hosting\Hosting;

use Torr\Hosting\Tier\HostingTier;

final readonly class HostingEnvironment
{
	private HostingTier $tier;

	/**
	 */
	public function __construct (
		HostingTier|string $tier,
		private bool $isDebug,
		private ?string $installationKey = null,
	)
	{
		$this->tier = \is_string($tier)
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
	public function getInstallationKey () : ?string
	{
		return $this->installationKey;
	}

	/**
	 * Whether the Symfony debug mode is active. This is separate from the app environment (like `prod` or `dev`) and controls whether extensive debug information is collected. 
	 * This mode is controlled via the `APP_DEBUG` environment variable.
	 */
	public function isDebug () : bool
	{
		return $this->isDebug;
	}
}
