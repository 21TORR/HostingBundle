<?php declare(strict_types=1);

namespace Torr\Hosting\Git;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Torr\Hosting\Git\Data\InstalledGitCommit;

final class GitVersionDumper
{
	private Filesystem $filesystem;
	private LoggerInterface $logger;
	private string $filePath;

	/**
	 */
	public function __construct (
		Filesystem $filesystem,
		LoggerInterface $logger,
		string $projectDir,
	)
	{
		$this->filesystem = $filesystem;
		$this->logger = $logger;
		$this->filePath = "{$projectDir}/.installed-git-version.json";
	}

	/**
	 */
	public function dump (?InstalledGitCommit $commit) : void
	{
		try {
			$content = null !== $commit
				? $commit->toArray()
				: null;

			$this->filesystem->dumpFile($this->filePath, \json_encode($content, \JSON_THROW_ON_ERROR));
		}
		catch (\JsonException $exception)
		{
			$this->logger->error("Could not write installed git version file: {message}", [
				"message" => $exception->getMessage(),
				"exception" => $exception,
			]);
		}
	}

	/**
	 * Loads the installed git commit from disk
	 */
	public function load () : ?InstalledGitCommit
	{
		if (!$this->filesystem->exists($this->filePath))
		{
			return null;
		}

		$content = @\file_get_contents($this->filePath);

		if (!$content)
		{
			return null;
		}

		try
		{
			$data = \json_decode($content, true, 512, \JSON_THROW_ON_ERROR);

			return \is_array($data)
				? InstalledGitCommit::fromArray($data)
				: null;
		}
		catch (\JsonException $exception)
		{
			$this->logger->error("Could not read installed git version file: {message}", [
				"message" => $exception->getMessage(),
				"exception" => $exception,
			]);

			return null;
		}
	}
}
