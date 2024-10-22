<?php declare(strict_types=1);

namespace Torr\Hosting\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Torr\Hosting\Event\HealthCheckLiveEvent;
use Torr\Hosting\Event\HealthCheckReadyEvent;

/**
 * @final
 */
class HealthApiController extends AbstractController
{
	/**
	 *
	 */
	public function liveEndpoint (
		EventDispatcherInterface $dispatcher,
	) : JsonResponse
	{
		return $this->runHealthCheck(
			$dispatcher->dispatch(new HealthCheckLiveEvent()),
		);
	}


	/**
	 *
	 */
	public function readyEndpoint (
		EventDispatcherInterface $dispatcher,
	) : Response
	{
		return $this->runHealthCheck(
			$dispatcher->dispatch(new HealthCheckReadyEvent()),
		);
	}

	/**
	 *
	 */
	private function runHealthCheck (
		HealthCheckLiveEvent|HealthCheckReadyEvent $event,
	) : JsonResponse
	{
		if (!$event->isHealthy())
		{
			return $this->json([
				"ok" => false,
				"failed" => $event->getFailedCheck(),
			], 503);
		}

		return $this->json([
			"ok" => true,
		]);
	}
}
