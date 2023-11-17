<?php declare(strict_types=1);

namespace Torr\Hosting\Tier;

/**
 */
enum HostingTier : string
{
	/**
	 * @deprecated
	 *
	 * @todo remove in v3
	 */
	case LIVE = "live";
	case PRODUCTION = "production";
	case STAGING = "staging";
	case DEVELOPMENT = "development";

	/**
	 *
	 */
	public static function getAllowedConfigValues () : array
	{
		$result = [];

		foreach (self::cases() as $tier)
		{
			if (self::LIVE !== $tier)
			{
				$result[] = $tier->value;
			}
		}

		return $result;
	}
}
