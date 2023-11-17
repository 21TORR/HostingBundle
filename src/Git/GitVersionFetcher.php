<?php declare(strict_types=1);

namespace Torr\Hosting\Git;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

final class GitVersionFetcher
{
	/**
	 */
	public function __construct (
		private readonly LoggerInterface $logger,
		private readonly string $projectDir,
	) {}


	/**
	 * @return array{commit:string, tag:string|null}|null
	 */
	public function detectVersion () : ?array
	{
		$executableFinder = new ExecutableFinder();
		$git = $executableFinder->find("git");

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

		return [
			"commit" => $commit,
			"last-tag" => $tag,
		];
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
		catch (ProcessFailedException)
		{
			return null;
		}
	}
}
