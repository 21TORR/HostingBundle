<?php declare(strict_types=1);

namespace Torr\Hosting\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Torr\Hosting\Deployment\TaskCli;
use Torr\Hosting\Deployment\TaskRunners;

#[AsCommand("hosting:run-tasks:post-build")]
final class PostBuildTasksCommand extends Command
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
	protected function execute (InputInterface $input, OutputInterface $output) : int
	{
		$io = new TaskCli($input, $output);
		$io->title("Run Post Build Tasks");

		$this->runners->runPostBuild($io);

		$io->newLine();
		$io->success("Run all post build tasks.");

		return 0;
	}
}
