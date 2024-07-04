<?php declare(strict_types=1);

namespace Torr\Hosting\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Torr\Hosting\Deployment\TaskCli;
use Torr\Hosting\Deployment\HookRunners;

#[AsCommand(
	"hosting:hook:deploy",
	description: "Runs the hooks for 'after the deployment finished'",
	aliases: ["hosting:run-tasks:post-deploy"]
)]
final class DeployHooksCommand extends Command
{
	private HookRunners $runners;

	/**
	 * @inheritDoc
	 */
	public function __construct(HookRunners $runners)
	{
		parent::__construct();
		$this->runners = $runners;
	}

	/**
	 * @inheritDoc
	 */
	protected function execute(InputInterface $input, OutputInterface $output) : int
	{
		$io = new TaskCli($input, $output);
		$io->title("Run Deploy Hooks");

		if ("hosting:hook:build" !== $input->getFirstArgument())
		{
			$message = sprintf(
				"The command `%s` was deprecated. Use `hosting:hook:deploy` instead.",
				$input->getFirstArgument(),
			);
			trigger_deprecation(
				"21torr/hosting",
				"3.1.0",
				$message,
			);
			$io->caution($message);
		}

		$this->runners->runPostDeployment($io);

		$io->newLine();
		$io->success("Ran all deploy hooks.");

		return 0;
	}
}
