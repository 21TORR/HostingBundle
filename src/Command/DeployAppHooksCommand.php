<?php declare(strict_types=1);

namespace Torr\Hosting\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Torr\Hosting\Deployment\HookRunners;
use Torr\Hosting\Deployment\TaskCli;

#[AsCommand(
	"hosting:hook:deploy-app",
	description: "Runs the hooks for deploying a complete application.",
)]
final class DeployAppHooksCommand extends Command
{
	/**
	 */
	public function __construct (
		private readonly HookRunners $runners,
	)
	{
		parent::__construct();
	}

	/**
	 */
	#[\Override]
	protected function execute (InputInterface $input, OutputInterface $output) : int
	{
		$io = new TaskCli($input, $output);
		$io->title("Run Deploy App Hooks");

		$this->runners->runDeployAppHooks($io);

		$io->newLine();
		$io->success("Ran all deploy app hooks.");

		return 0;
	}
}
