<?php

namespace VZenix\Bundle\ContactBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration structure for contact bundle
 * @author Francisco Muros Espadas <paco@vzenix.es>
 */
class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('contact');

        $rootNode
            ->children()
                ->arrayNode("mail")
                    ->children()
                        ->scalarNode("subject")->end()
                        ->scalarNode("to")->end()
                        ->scalarNode("from")->end()
                    ->end()
                ->end()
                ->arrayNode("templates")
                    ->children()
                        ->scalarNode("view")->end()
                        ->scalarNode("mails")->end()
                    ->end()
                ->end()
                ->scalarNode("lapsus")->end()
                ->booleanNode("log")->end()
                ->booleanNode('swiftmailer')->end()
            ->end()
        ;
        
        return $treeBuilder;
    }

}
