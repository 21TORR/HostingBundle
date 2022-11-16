<?php declare(strict_types=1);

namespace Torr\Hosting\BuildInfo;

use Symfony\Component\Filesystem\Filesystem;
use Torr\Hosting\Exception\InvalidBuildInfoException;

final class BuildInfo
{
	private ?array $info = null;

	/**
	 */
	public function __construct (
		private readonly Filesystem $filesystem,
		private readonly string $filePath,
	) {}

	/**
	 *
	 */
	public function get (string $key) : ?string
	{
		return $this->fetchInfo()[$key] ?? null;
	}

	/**
	 *
	 */
	public function set (string $key, ?string $value) : self
	{
		$this->info = \array_replace(
			$this->fetchInfo(),
			[$key => $value],
		);
		$this->writeJson();

		return $this;
	}

	/**
	 */
	public function reset () : void
	{
		$this->info = [];
		$this->writeJson();
	}


	/**
	 *
	 */
	private function fetchInfo () : array
	{
		if (null === $this->info)
		{
			$this->info = $this->readJson();
		}

		return $this->info;
	}


	/**
	 */
	private function readJson () : array
	{
		if (!$this->filesystem->exists($this->filePath))
		{
			return [];
		}

		$content = @\file_get_contents($this->filePath);

		if (false === $content)
		{
			throw new InvalidBuildInfoException("Build info file exists but is not readable");
		}

		try
		{
			$data = \json_decode($content, true, flags: \JSON_THROW_ON_ERROR);

			if (!\is_array($data) || !\is_array($data["data"] ?? null))
			{
				throw new InvalidBuildInfoException("Invalid build info JSON: must be an array");
			}

			return $data["data"];
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
	private function writeJson () : void
	{
		try
		{
			$data = [
				"_meta" => [
					"created" => (new \DateTimeImmutable())->format("c"),
				],
				"data" => $this->fetchInfo(),
			];

			$this->filesystem->dumpFile(
				$this->filePath,
				\json_encode($data, flags: \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT),
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
