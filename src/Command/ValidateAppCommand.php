<?php declare(strict_types=1);

namespace Torr\Hosting\Command;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Torr\Cli\Console\Style\TorrStyle;
use Torr\Hosting\Event\ValidateAppEvent;

/**
 * @final
 */
#[AsCommand(
	"hosting:validate-app",
	description: "Validates the app configuration, for usage in the CI before deployment",
)]
class ValidateAppCommand extends Command
{
	/**
	 */
	public function __construct (
		private readonly EventDispatcherInterface $dispatcher,
		private readonly LoggerInterface $logger,
	)
	{
		parent::__construct();
	}

	/**
	 */
	protected function execute (InputInterface $input, OutputInterface $output) : int
	{
		$io = new TorrStyle($input, $output);
		$io->title("Hosting: Validate App");

		try
		{
			$event = new ValidateAppEvent($io);
			$this->dispatcher->dispatch($event);
			$failedCheck = $event->getFailedCheck();
		}
		catch (\Exception $exception)
		{
			$this->logger->error("Failed to validate app", [
				"exception" => $exception,
			]);
			$failedCheck = $exception->getMessage();
		}

		if (null !== $failedCheck)
		{
			$io->error(\sprintf(
				"Validation failed: %s",
				$failedCheck,
			));

			return self::FAILURE;
		}

		$io->success("App is valid");

		return self::SUCCESS;
	}
}
