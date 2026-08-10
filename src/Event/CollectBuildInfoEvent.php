<?php declare(strict_types=1);

namespace Torr\Hosting\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class CollectBuildInfoEvent extends Event
{
	/** @var array<array-key, string|float|int|bool|null> */
	private array $info = [];

	/**
	 * @return $this
	 */
	public function set (string $key, string|float|int|bool|null $value) : self
	{
		$this->info[$key] = $value;

		return $this;
	}

	/**
	 */
	public function getInfo () : array
	{
		return $this->info;
	}
}
