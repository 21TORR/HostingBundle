<?php declare(strict_types=1);

namespace Torr\Hosting\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Torr\Hosting\Deployment\HookRunners;
use Torr\Hosting\Deployment\TaskCli;

#[AsCommand(
	"hosting:hook:deploy-container",
	description: "Runs the hooks for deploying a single container",
	aliases: [
		"hosting:hook:deploy",
	],
)]
final readonly class DeployContainerHooksCommand
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
		$io->title("Run Deploy Container Hooks");

		if ("hosting:hook:deploy-container" !== $input->getFirstArgument())
		{
			$io->caution("Always call this command with `hosting:hook:deploy-container`, all other names are deprecated.");

			/** @phpstan-ignore-next-line todoBy.sfDeprecation (our own deprecation) */
			trigger_deprecation("21torr/hosting", "4.2.1", "Always call this command with `hosting:hook:deploy-container`, all other names are deprecated");
		}

		$this->runners->runDeployContainerHooks($io);

		$io->newLine();
		$io->success("Ran all deploy container hooks.");

		return Command::SUCCESS;
	}
}
