<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment;

use Torr\Cli\Console\Style\TorrStyle;

final class TaskCli extends TorrStyle
{
	/**
	 *
	 */
	public function done (string $message) : void
	{
		$this->write(\sprintf(
			"<fg=green>✓</> %s",
			$message,
		));
	}
}
