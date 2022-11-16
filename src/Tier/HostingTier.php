<?php declare(strict_types=1);

namespace Torr\Hosting\Tier;

enum HostingTier : string
{
	case LIVE = "live";

	case STAGING = "staging";

	case DEVELOPMENT = "development";

	/**
	 *
	 */
	public static function getAllowedConfigValues () : array
	{
		return \array_map(
			static fn (self $tier) => $tier->value,
			self::cases(),
		);
	}
}
