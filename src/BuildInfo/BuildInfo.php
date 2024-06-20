<?php declare(strict_types=1);

namespace Torr\Hosting\BuildInfo;

final readonly class BuildInfo
{
	public function __construct (
		/** @var array<array-key, string|float|int|bool|null> */
		private ?array $info,
	) {}

	/**
	 *
	 */
	public function get (string $key) : string|float|int|bool|null
	{
		return ($this->info ?? [])[$key] ?? null;
	}

	/**
	 * @return array<array-key, string|float|int|bool|null>
	 */
	public function getAll () : array
	{
		return $this->info ?? [];
	}

	/**
	 * Returns whether the data was actually stored
	 */
	public function isStored () : bool
	{
		return null !== $this->info;
	}
}
