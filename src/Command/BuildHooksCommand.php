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
	aliases: ["hosting:run-tasks:post-build"],
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

		// @todo remove code block + alias in v4
		if ("hosting:hook:build" !== $input->getFirstArgument())
		{
			$message = \sprintf(
				"The command `%s` was deprecated. Use `hosting:hook:build` instead.",
				$input->getFirstArgument(),
			);
			trigger_deprecation(
				"21torr/hosting",
				"3.1.0",
				$message,
			);
			$io->caution($message);
		}

		$this->runners->runBuildHooks($io);

		$io->newLine();
		$io->success("Ran all build hooks.");

		return 0;
	}
}
