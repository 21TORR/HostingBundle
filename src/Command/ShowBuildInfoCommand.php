<?php declare(strict_types=1);

namespace Torr\Hosting\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Torr\Cli\Console\Style\TorrStyle;
use Torr\Hosting\BuildInfo\BuildInfoStorage;

#[AsCommand("hosting:build:info")]
final class ShowBuildInfoCommand extends Command
{
	/**
	 * @inheritDoc
	 */
	public function __construct (
		private readonly BuildInfoStorage $buildInfoStorage,
	)
	{
		parent::__construct();
	}


	/**
	 * @inheritDoc
	 */
	protected function configure () : void
	{
		$this->setDescription("Shows the current build info");
	}

	/**
	 * @inheritDoc
	 */
	protected function execute (InputInterface $input, OutputInterface $output) : int
	{
		$io = new TorrStyle($input, $output);
		$io->title("Hosting: Build Info");

		$info = $this->buildInfoStorage->getBuildInfo();
		$entries = $info->getAll();
		\uksort($entries, "strnatcasecmp");

		$rows = [];

		foreach ($entries as $key => $value)
		{
			$rows[] = [
				\sprintf("<fg=yellow>%s</>", $key),
				$this->formatValue($value),
			];
		}

		$io->table(["Key", "Value"], $rows);
		return self::SUCCESS;
	}

	/**
	 */
	private function formatValue (mixed $value) : mixed
	{
		return match ($value)
		{
			null => "<fg=gray>null</>",
			true => "<fg=green>true</>",
			false => "<fg=red>false</>",
			default => $value,
		};
	}
}
