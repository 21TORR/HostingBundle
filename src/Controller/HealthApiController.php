<?php declare(strict_types=1);

namespace Torr\Hosting\Controller;

use Psr\Cache\CacheItemInterface;
use Psr\Clock\ClockInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Torr\Hosting\Event\HealthCheckLiveEvent;
use Torr\Hosting\Event\HealthCheckReadyEvent;

/**
 * @final
 */
class HealthApiController extends AbstractController
{
	private const string CACHE_KEY = "hosting.health_check.%s";

	/**
	 *
	 */
	public function healthCheckEndpoint (
		EventDispatcherInterface $dispatcher,
		CacheInterface $cache,
		ClockInterface $clock,
		string $type,
	) : JsonResponse
	{
		$event = match ($type)
		{
			"live" => new HealthCheckLiveEvent(),
			"ready" => new HealthCheckReadyEvent(),
			default => throw $this->createNotFoundException("Unknown health check type \"{$type}\""),
		};

		$failedCheck = $cache->get(
			\sprintf(self::CACHE_KEY, $type),
			static function (CacheItemInterface $item) use ($dispatcher, $event)
			{
				// cache for at most 60s
				$item->expiresAfter(60);

				return $dispatcher->dispatch($event)->getFailedCheck();
			},
		);

		// check whether any check failed
		if (null !== $failedCheck)
		{
			return $this->json([
				"ok" => false,
				"failed" => $failedCheck,
				"checked" => $clock->now()->format("c"),
			], 503);
		}

		return $this->json([
			"ok" => true,
			"checked" => $clock->now()->format("c"),
		]);
	}
}
