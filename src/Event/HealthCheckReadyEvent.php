<?php declare(strict_types=1);

namespace Torr\Hosting\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * @final
 */
class HealthCheckReadyEvent extends Event
{
	private ?string $failedCheck = null;

	/**
	 *
	 */
	public function markAsFailed (string $failedCheck) : void
	{
		$this->failedCheck = $failedCheck;
		$this->stopPropagation();
	}

	/**
	 *
	 */
	public function isHealthy () : bool
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
