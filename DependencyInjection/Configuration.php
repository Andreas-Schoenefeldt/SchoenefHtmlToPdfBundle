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

    const pageSizes = ['A0', 'A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'A9', 'B0', 'B1', 'B1', 'B2', 'B3', 'B4', 'B5', 'B6', 'B7', 'B8', 'B9', 'C5E', 'Comm10E', 'DLE', 'Executive', 'Folio', 'Ledger', 'Legal', 'Letter', 'Tabloid'];
    const PROVIDER_PDF_ROCKET = 'pdfrocket';

    const KEY_PROVIDER = 'provider';
    const KEY_TIMEOUT = 'timeout';
    const KEY_APIKEY = 'apikey';
    const KEY_DEFAULT_OPTIONS = 'default_options';

    const OPTION_DPI = 'dpi';
    const OPTION_SHRINKING = 'shrinking';
    const OPTION_IMAGE_QUALITY = 'image_quality';
    const OPTION_PAGE_SIZE = 'page_size';
    const OPTION_ZOOM = 'zoom';
    const OPTION_JS_DELAY = 'js_delay';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('schoenef_html_to_pdf');

        $treeBuilder->getRootNode()
            ->children()
                ->enumNode(self::KEY_PROVIDER)->values([self::PROVIDER_PDF_ROCKET])->defaultValue(self::PROVIDER_PDF_ROCKET)->end()
                ->integerNode(self::KEY_TIMEOUT)->defaultValue(20)->end()
                ->scalarNode(self::KEY_APIKEY)->isRequired()->end()
                ->arrayNode(self::KEY_DEFAULT_OPTIONS)
                    ->children()
                        ->integerNode(self::OPTION_DPI)->end()
                        ->floatNode(self::OPTION_ZOOM)->end()
                        ->integerNode(self::OPTION_JS_DELAY)->end()
                        ->booleanNode(self::OPTION_SHRINKING)->defaultTrue()->end()
                        ->integerNode(self::OPTION_IMAGE_QUALITY)->end()
                        ->enumNode(self::OPTION_PAGE_SIZE)->values(self::pageSizes)->end()
                    ->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}