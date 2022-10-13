<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment\Task;

use Symfony\Component\Console\Style\SymfonyStyle;
use Torr\Hosting\Deployment\PostBuildTaskInterface;
use Torr\Hosting\Git\GitVersion;

final class DumpGitBuildTask implements PostBuildTaskInterface
{
	private GitVersion $gitVersion;

	/**
	 * @inheritDoc
	 */
	public function __construct (GitVersion $gitVersion)
	{
		$this->gitVersion = $gitVersion;
	}


	/**
	 * @inheritDoc
	 */
	public function getLabel() : string
	{
		return "Dump Git Version";
	}


	/**
	 * @inheritDoc
	 */
	public function runPostBuild(SymfonyStyle $io) : void
	{
		$io->comment("Refreshing the version");
		$version = $this->gitVersion->refresh();

		if (null !== $version)
		{
			$io->writeln("Found version:");
			$io->writeln(\sprintf("    Commit: <fg=blue>%s</>", $version->getCommit()));
			$io->writeln(\sprintf("    Tag:    <fg=blue>%s</>", $version->getTag() ?? "—"));
		}
		else
		{
			$io->writeln("Found no installed version.");
		}
	}
}
