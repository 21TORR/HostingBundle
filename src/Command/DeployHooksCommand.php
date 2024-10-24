<?php declare(strict_types=1);

namespace Torr\Hosting\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Torr\Hosting\Deployment\HookRunners;
use Torr\Hosting\Deployment\TaskCli;

#[AsCommand(
	"hosting:hook:deploy",
	description: "Runs the hooks for 'after the deployment finished'",
)]
final class DeployHooksCommand extends Command
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
	protected function execute (InputInterface $input, OutputInterface $output) : int
	{
		$io = new TaskCli($input, $output);
		$io->title("Run Deploy Hooks");

		$this->runners->runDeployHooks($io);

		$io->newLine();
		$io->success("Ran all deploy hooks.");

		return 0;
	}
}
