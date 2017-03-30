<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 30/03/17
 * Time: 16:10
 */

namespace Schoenef\HtmlToPdfBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 * this is testing the configuration in the following manner:
 * html2pdf:
 *   provider: defualt pdfrocket
 *   timeout: default 20
 *   apikey: required
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        // the root must be the name of the bundle: http://stackoverflow.com/a/35505189/2776727
        $rootNode = $treeBuilder->root('html_to_pdf');

        $rootNode
            ->children()
                ->scalarNode('provider')
                    ->defaultValue('pdfrocket')
                ->end()
                ->scalarNode('timeout')
                    ->defaultValue(20)
                ->end()
                ->scalarNode('apikey')
                    ->isRequired()
                ->end()
            ->end()
        ->end();
        return $treeBuilder;
    }
}