<?php declare(strict_types=1);

namespace Torr\Hosting\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Torr\Hosting\Tier\HostingTier;

final class HostingBundleConfiguration implements ConfigurationInterface
{
	/**
	 * @inheritDoc
	 */
	public function getConfigTreeBuilder() : TreeBuilder
	{
		$treeBuilder = new TreeBuilder("hosting");

		$treeBuilder->getRootNode()
			->children()
				->enumNode("tier")
					->values(HostingTier::getAllowedConfigValues())
					->info("The deployment tier of the current installation")
				->end()
				->scalarNode("installation")
					->defaultNull()
					->setDeprecated("21torr/hosting", "2.1.0", "The installation key is deprecated.")
					->info("An unique identifier to identify this unique project installation")
					->validate()
						->ifTrue(static function ($value) { return null !== $value && \preg_match('~[^a-z0-9_-]~', $value); })
						->thenInvalid("The installation key may only consist of a-z 0-9 '_' and '-'.")
					->end()
				->end()
			->end();

		return $treeBuilder;
	}
}
