<?php declare(strict_types=1);

namespace Torr\Hosting\BuildInfo;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Torr\Hosting\Event\CollectBuildInfoEvent;
use Torr\Hosting\Exception\InvalidBuildInfoException;

final class BuildInfoStorage
{
	private ?BuildInfo $cache = null;

	/**
	 */
	public function __construct (
		private readonly EventDispatcherInterface $dispatcher,
		private readonly Filesystem $filesystem,
		private readonly string $filePath,
	) {}

	/**
	 */
	public function getBuildInfo () : BuildInfo
	{
		return $this->cache ??= $this->loadBuildInfo();
	}

	/**
	 *
	 */
	private function loadBuildInfo () : BuildInfo
	{
		if (!$this->filesystem->exists($this->filePath))
		{
			return new BuildInfo(null);
		}

		$content = @file_get_contents($this->filePath);

		if (false === $content)
		{
			throw new InvalidBuildInfoException("Build info file exists but is not readable");
		}

		try
		{
			$data = json_decode($content, true, flags: \JSON_THROW_ON_ERROR);

			if (!\is_array($data))
			{
				throw new InvalidBuildInfoException("Invalid build info JSON: must be an array");
			}

			// sort info before passing it to build info
			uksort($data, "strnatcasecmp");

			/** @var array<array-key, string|float|int|bool|null> $data */
			return new BuildInfo($data);
		}
		catch (\JsonException $exception)
		{
			throw new InvalidBuildInfoException(
				\sprintf("Invalid build info JSON: %s", $exception->getMessage()),
				previous: $exception,
			);
		}
	}

	/**
	 *
	 */
	public function refresh () : void
	{
		// remove existing file
		$this->filesystem->remove($this->filePath);
		$this->cache = null;

		// refetch build info
		$event = new CollectBuildInfoEvent();
		$this->dispatcher->dispatch($event);

		// write the data sorted into the JSON, makes debugging easier
		$info = $event->getInfo();
		uksort($info, "strnatcasecmp");

		try
		{
			$this->filesystem->dumpFile(
				$this->filePath,
				json_encode($info, flags: \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT),
			);
		}
		catch (\JsonException $exception)
		{
			throw new InvalidBuildInfoException(
				\sprintf("Failed to write build info JSON: %s", $exception->getMessage()),
				previous: $exception,
			);
		}
	}
}
