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
final class BuildHooksCommand extends Command
{
	private HookRunners $runners;

	/**
	 */
	public function __construct (HookRunners $runners)
	{
		parent::__construct();
		$this->runners = $runners;
	}

	/**
	 */
	#[\Override]
	protected function execute (InputInterface $input, OutputInterface $output) : int
	{
		$io = new TaskCli($input, $output);
		$io->title("Run Build Hooks");

		$this->runners->runBuildHooks($io);

		$io->newLine();
		$io->success("Ran all build hooks.");

		return 0;
	}
}
