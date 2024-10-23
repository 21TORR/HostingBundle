<?php declare(strict_types=1);

namespace Torr\Hosting\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Torr\Cli\Console\Style\TorrStyle;

/**
 * This event is dispatched to validate the app.
 * It is supposed to check internal configuration and settings.
 *
 * Be aware, that it is supposed to be run in the CI, so you might not have access to some external services (like
 * the database).
 *
 * @final
 */
class ValidateAppEvent extends Event
{
	private ?string $failedCheck = null;

	/**
	 *
	 */
	public function __construct (
		public readonly TorrStyle $io,
	) {}

	/**
	 *
	 */
	public function markAppAsInvalid (string $failedCheck) : void
	{
		$this->failedCheck = $failedCheck;
		$this->stopPropagation();
	}

	/**
	 *
	 */
	public function isValid () : bool
	{
		return null === $this->failedCheck;
	}

	/**
	 *
	 */
	public function getFailedCheck () : ?string
	{
		return $this->failedCheck;
	}
}
