<?php declare(strict_types=1);

namespace Torr\Hosting\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Torr\Hosting\Deployment\HookRunners;
use Torr\Hosting\Deployment\TaskCli;

#[AsCommand(
	"hosting:hook:build",
	description: "Runs the hooks for 'after the build finished'",
)]
final readonly class BuildHooksCommand
{
	/**
	 */
	public function __construct (
		private HookRunners $runners,
	) {}

	/**
	 */
	public function __invoke (InputInterface $input, OutputInterface $output) : int
	{
		$io = new TaskCli($input, $output);
		$io->title("Run Build Hooks");

		$this->runners->runBuildHooks($io);

		$io->newLine();
		$io->success("Ran all build hooks.");

		return Command::SUCCESS;
	}
}
