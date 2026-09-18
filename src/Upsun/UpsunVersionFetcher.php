<?php declare(strict_types=1);

namespace Torr\Hosting\Upsun;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Fetches the version from the environment of an Upsun build.
 *
 * Upsun checks out the sources without their `.git` directory, so the tree id is all that is available
 * there. It is read from the environment directly, so that no build hook needs to pass it along.
 *
 * @final
 */
class UpsunVersionFetcher
{
	/**
	 */
	public function __construct (
		#[Autowire(env: "default::PLATFORM_TREE_ID")]
		private readonly ?string $treeId,
	) {}

	/**
	 * @return array{tree-id:string}|null
	 */
	public function detectVersion () : ?array
	{
		if (null === $this->treeId || "" === $this->treeId)
		{
			return null;
		}

		return [
			"tree-id" => $this->treeId,
		];
	}
}
