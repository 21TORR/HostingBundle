<?php declare(strict_types=1);

namespace Torr\Hosting\Tier;

/**
 */
enum HostingTier : string
{
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
			$result[] = $tier->value;
		}

		return $result;
	}
}
