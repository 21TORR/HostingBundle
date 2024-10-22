<?php declare(strict_types=1);

namespace Torr\Hosting\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaValidator;
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
		private ?EntityManagerInterface $entityManager = null,
	) {}

	#[AsEventListener]
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

			return;
		}

		if (!$validator->schemaInSyncWithMetadata())
		{
			$event->markAsFailed("doctrine: database out of sync");
		}
	}
}
