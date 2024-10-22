<?php declare(strict_types=1);

namespace Torr\Hosting\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event to add health checks for your application.
 *
 * This implements the "live" event, so the container should/can be restarted, if this event fails.
 *
 * Please note, that you should only mark something as "failed" if it is a problem that can potentially
 * be fixed via a restart of the container. So you should not check permanent config errors or
 * similar things here.
 *
 * @final
 */
class HealthCheckLiveEvent extends Event
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
