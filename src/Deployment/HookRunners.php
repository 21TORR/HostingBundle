<?php declare(strict_types=1);

namespace Torr\Hosting\Deployment;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Torr\Hosting\Deployment\Exception\HookFailedException;

final readonly class HookRunners
{
	public const string TAG_BUILD_HOOK = "hosting.hook.build";
	public const string TAG_DEPLOY_CONTAINER_HOOK = "hosting.hook.deploy-container";
	public const string TAG_DEPLOY_APP_HOOK = "hosting.hook.deploy-app";

	/**
	 */
	public function __construct (
		/** @var iterable<BuildHookInterface> */
		#[AutowireIterator(tag: self::TAG_BUILD_HOOK)]
		private iterable $buildHooks,
		/** @var iterable<DeployContainerHookInterface|DeployHookInterface> */
		#[AutowireIterator(tag: self::TAG_DEPLOY_CONTAINER_HOOK)]
		private iterable $deployContainerHooks,
		/** @var iterable<DeployAppHookInterface> */
		#[AutowireIterator(tag: self::TAG_DEPLOY_APP_HOOK)]
		private iterable $deployAppHooks,
		private LoggerInterface $logger,
	) {}

	/**
	 * Runs all build hooks.
	 *
	 * @return bool whether all hooks ran successfully. Stops at the first failed hook.
	 */
	public function runBuildHooks (TaskCli $io) : bool
	{
		$first = true;

		foreach ($this->buildHooks as $hook)
		{
			$success = $this->runHook(
				$io,
				"Build",
				$hook->getLabel(),
				$first,
				static fn () => $hook->runPostBuild($io),
			);

			if (!$success)
			{
				return false;
			}

			$first = false;
		}

		return true;
	}

	/**
	 * Runs all deploy container hooks.
	 *
	 * @return bool whether all hooks ran successfully. Stops at the first failed hook.
	 */
	public function runDeployContainerHooks (TaskCli $io) : bool
	{
		$first = true;

		foreach ($this->deployContainerHooks as $hook)
		{
			$success = $this->runHook(
				$io,
				"Deploy Container",
				$hook->getLabel(),
				$first,
				static function () use ($hook, $io) : void
				{
					if ($hook instanceof DeployHookInterface)
					{
						trigger_deprecation(
							"21torr/hosting",
							"4.2.1",
							\sprintf(
								"Using 'deploy hooks' is deprecated, use 'DeployContainerHook' instead. Used in '%s'",
								$hook::class,
							),
						);

						$hook->runPostDeployment($io);

						return;
					}

					$hook->runDeployContainer($io);
				},
			);

			if (!$success)
			{
				return false;
			}

			$first = false;
		}

		return true;
	}

	/**
	 * Runs all deploy app hooks.
	 *
	 * @return bool whether all hooks ran successfully. Stops at the first failed hook.
	 */
	public function runDeployAppHooks (TaskCli $io) : bool
	{
		$first = true;

		foreach ($this->deployAppHooks as $hook)
		{
			$success = $this->runHook(
				$io,
				"Deploy App",
				$hook->getLabel(),
				$first,
				static fn () => $hook->runDeployApp($io),
			);

			if (!$success)
			{
				return false;
			}

			$first = false;
		}

		return true;
	}

	/**
	 * Runs a single hook and reports it, if it failed.
	 *
	 * @param callable(): void $run
	 *
	 * @return bool whether the hook ran successfully
	 */
	private function runHook (TaskCli $io, string $type, string $label, bool $isFirst, callable $run) : bool
	{
		if (!$isFirst)
		{
			$io->newLine(2);
		}

		$io->section("Run {$type} Hook: <fg=magenta>{$label}</>");

		try
		{
			$run();

			return true;
		}
		catch (HookFailedException $exception)
		{
			$this->logger->error("{type} hook '{label}' failed: {message}", [
				"type" => $type,
				"label" => $label,
				"message" => $exception->getMessage(),
				"exception" => $exception,
			]);

			$io->newLine();
			$io->error(\sprintf(
				"%s hook '%s' failed: %s",
				$type,
				$label,
				$exception->getMessage(),
			));

			return false;
		}
	}
}
