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
	public function getConfigTreeBuilder()
	{
		$treeBuilder = new TreeBuilder("hosting");

		$treeBuilder->getRootNode()
			->children()
				->enumNode("tier")
					->isRequired()
					->values(HostingTier::getAllowedTiers())
					->info("The deployment tier of the current installation")
				->end()
				->scalarNode("installation")
					->isRequired()
					->info("An unique identifier to identify this unique project installation")
					->validate()
						->ifTrue(static function ($value) { return \preg_match('~[^a-z0-9_-]~', $value); })
						->thenInvalid("The installation key may only consist of a-z 0-9 '_' and '-'.")
					->end()
				->end()
			->end();

		return $treeBuilder;
	}
}
