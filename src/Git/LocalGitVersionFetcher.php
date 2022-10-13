<?php declare(strict_types=1);

namespace Torr\Hosting\Git;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Torr\Hosting\Git\Data\InstalledGitCommit;

final class LocalGitVersionFetcher
{
	private LoggerInterface $logger;
	private string $projectDir;
	private ?string $git = null;
	private bool $gitFetched = false;

	/**
	 */
	public function __construct (
		LoggerInterface $logger,
		string $projectDir,
	)
	{
		$this->logger = $logger;
		$this->projectDir = $projectDir;
	}

	/**
	 */
	public function loadLocalVersion () : ?InstalledGitCommit
	{
		$git = $this->getGit();

		if (null === $git)
		{
			return null;
		}

		$commit = $this->run([$git, "rev-parse", "HEAD"]);
		$tag = $this->run([$git, "describe", "--abbrev=0"]);

		if (!\is_string($commit))
		{
			$this->logger->error("Could not fetch current installed version: no current commit.");
			return null;
		}

		return new InstalledGitCommit($commit, $tag);
	}


	/**
	 * Returns the path to the git executable
	 */
	private function getGit () : ?string
	{
		if (!$this->gitFetched)
		{
			$this->git = $this->run(["command", "-v", "git"]);
			$this->gitFetched = true;

			if (null === $this->git)
			{
				$this->logger->error("Could not fetch current installed version:  `git` could not be found.");
			}
		}

		return $this->git;
	}


	/**
	 * Runs the given command in the project dir
	 */
	private function run (array $command) : ?string
	{
		try
		{
			$process = new Process($command, $this->projectDir);
			$process->mustRun();
			$result = \trim($process->getOutput());

			return "" !== $result
				? $result
				: null;
		}
		catch (ProcessFailedException $exception)
		{
			return null;
		}
	}
}
