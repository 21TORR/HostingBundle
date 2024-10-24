<?php declare(strict_types=1);

namespace Torr\Hosting\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaValidator;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Torr\Hosting\Event\HealthCheckLiveEvent;

/**
 * @final
 */
readonly class DoctrineHealthCheckListener
{
	/**
	 */
	public function __construct (
		private LoggerInterface $logger,
		private ?EntityManagerInterface $entityManager = null,
	) {}

	#[AsEventListener(priority: -100)]
	public function onLiveCheck (HealthCheckLiveEvent $event) : void
	{
		if (null === $this->entityManager || !class_exists(SchemaValidator::class))
		{
			return;
		}

		$validator = new SchemaValidator($this->entityManager);
		$result = $validator->validateMapping();

		if (!empty($result))
		{
			$event->markAsFailed("doctrine: invalid mapping");
			$this->logger->critical("Failed doctrine health check: invalid mapping", [
				"issues" => $result,
			]);

			return;
		}

		$databaseUpdateSchemaList = $validator->getUpdateSchemaList();

		if (\count($databaseUpdateSchemaList) > 0)
		{
			$event->markAsFailed("doctrine: database out of sync");
			$this->logger->critical("Failed doctrine health check: database out of sync", [
				"updateSchemaList" => $databaseUpdateSchemaList,
			]);
		}
	}
}
