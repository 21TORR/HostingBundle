<?php declare(strict_types=1);

namespace Torr\Hosting\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Torr\Hosting\Deployment\TaskCli;
use Torr\Hosting\Deployment\TaskRunners;

#[AsCommand("hosting:run-tasks:post-deploy", aliases: ["hosting:post-deploy"])]
final class PostDeploymentTasksCommand extends Command
{
	private TaskRunners $runners;

	/**
	 * @inheritDoc
	 */
	public function __construct(TaskRunners $runners)
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

		// @todo remove in v3 and remove alias above
		if ("hosting:run-tasks:post-deploy" !== $input->getFirstArgument())
		{
			trigger_deprecation("21torr/hosting", "2.1.0", "The command name '{$input->getFirstArgument()}' was deprecated. Use 'hosting:run-tasks:post-deploy' instead.");
			$io->caution(sprintf(
				"The command name `%s` was deprecated.\nUse `hosting:run-tasks:post-deploy` instead.",
				$input->getFirstArgument(),
			));
		}

		$io = new TaskCli($input, $output);
		$io->title("Run Post Deployment Tasks");

		$this->runners->runPostDeployment($io);

		$io->newLine();
		$io->success("Run all post deployment tasks.");

		return 0;
	}
}
