<?php declare(strict_types=1);

namespace Torr\Hosting\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class HostingBundleConfiguration implements ConfigurationInterface
{
	/**
	 */
	#[\Override]
	public function getConfigTreeBuilder () : TreeBuilder
	{
		$treeBuilder = new TreeBuilder("hosting");

		$treeBuilder->getRootNode()
			->children()
				// as we want to allow env vars here, we can't add the validation here, instead we
				// need to validate it at runtime
				->scalarNode("tier")
					->isRequired()
					->info("The deployment tier of the current installation")
				->end()
				->scalarNode("installation")
					->defaultNull()
					->info("An unique identifier to identify this unique project installation")
					->validate()
						->ifTrue(static fn ($value) => 0 !== preg_match('~[^a-z0-9_-]~', $value))
						->thenInvalid("The installation key may only consist of a-z 0-9 '_' and '-'.")
					->end()
				->end()
			->end();

		return $treeBuilder;
	}
}
