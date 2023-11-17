<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment\Task;

use Torr\Hosting\BuildInfo\BuildInfo;
use Torr\Hosting\Deployment\PostBuildTaskInterface;
use Torr\Hosting\Deployment\TaskCli;
use Torr\Hosting\Git\GitVersionFetcher;

final readonly class DumpGitBuildTask implements PostBuildTaskInterface
{
	/**
	 */
	public function __construct (
		private GitVersionFetcher $localGitVersionFetcher,
		private BuildInfo $buildInfo,
	) {}


	/**
	 * @inheritDoc
	 */
	public function getLabel () : string
	{
		return "Dump Git Version";
	}


	/**
	 * @inheritDoc
	 */
	public function runPostBuild (TaskCli $io) : void
	{
		$version = $this->localGitVersionFetcher->detectVersion();

		if (null !== $version)
		{
			$io->comment("Refreshing the version");
			$this->buildInfo
				->set("git-commit", $version["commit"])
				->set("git-tag", $version["tag"]);

			$io->writeln("Found version:");
			$io->writeln(\sprintf("    Commit: <fg=blue>%s</>", $version["commit"]));
			$io->writeln(\sprintf("    Tag:    <fg=blue>%s</>", $version["tag"] ?? "—"));
		}
		else
		{
			$io->writeln("Found no installed version.");
		}
	}
}
