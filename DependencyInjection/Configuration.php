<?php

namespace Ibtikar\ShareEconomyToolsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ibtikar_share_economy_tools');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode
                ->children()
                ->scalarNode('firebase_database_secret')
                ->end()
                ->scalarNode('firebase_url_base')
                ->end()
                ->scalarNode('google_distance_matrix_url_base')
                ->end()
                ->scalarNode('google_distance_matrix_key')
                ->end()
                ->scalarNode('google_directions_url_base')
                ->end()
                ->scalarNode('google_directions_key')
                ->end()
                ->end();
        return $treeBuilder;
    }

}
