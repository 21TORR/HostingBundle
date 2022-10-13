<?php declare(strict_types=1);

namespace Torr\Hosting\Git\Data;

final class InstalledGitCommit
{
	private string $commit;
	private ?string $tag;

	/**
	 */
	public function __construct (string $commit, ?string $tag)
	{
		$this->commit = $commit;
		$this->tag = $tag;
	}

	/**
	 */
	public function getCommit() : string
	{
		return $this->commit;
	}

	/**
	 */
	public function getTag() : ?string
	{
		return $this->tag;
	}

	/**
	 */
	public function toArray () : array
	{
		return [
			"commit" => $this->commit,
			"tag" => $this->tag,
		];
	}

	/**
	 */
	public static function fromArray (array $data) : ?self
	{
		return (
			\array_key_exists("commit", $data)
			&& \array_key_exists("tag", $data)
			&& \is_string($data["commit"])
			&& (null === $data["tag"] || \is_string($data["tag"]))
		)
			? new self($data["commit"], $data["tag"])
			: null;
	}
}
