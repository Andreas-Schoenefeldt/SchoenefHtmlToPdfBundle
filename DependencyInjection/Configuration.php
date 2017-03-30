<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 30/03/17
 * Time: 16:10
 */

namespace Schoenef\HtmlToPdfBundle\DependencyInjection;


use Schoenef\HtmlToPdfBundle\Service\Html2PdfConnector;
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
    const PROVIDER_PDFROCKET = 'pdfrocket';

    const KEY_PROVIDER = 'provider';
    const KEY_TIMEOUT = 'timeout';
    const KEY_APIKEY = 'apikey';
    const KEY_DEFAULT_OPTIONS = 'default_options';

    const OPTION_DPI = 'dpi';
    const OPTION_SHRINKING = 'shrinking';
    const OPTION_IMAGE_QUALITY = 'image_quality';
    const OPTION_PAGE_SIZE = 'page_size';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        // the root must be the name of the bundle: http://stackoverflow.com/a/35505189/2776727
        $rootNode = $treeBuilder->root('schoenef_html_to_pdf');

        $rootNode
            ->children()
                ->enumNode(self::KEY_PROVIDER)->values([self::PROVIDER_PDFROCKET])->defaultValue(self::PROVIDER_PDFROCKET)->end()
                ->integerNode(self::KEY_TIMEOUT)->defaultValue(20)->end()
                ->scalarNode(self::KEY_APIKEY)->isRequired()->end()
                ->arrayNode(self::KEY_DEFAULT_OPTIONS)
                    ->children()
                        ->integerNode(self::OPTION_DPI)->end()
                        ->booleanNode(self::OPTION_SHRINKING)->defaultTrue()->end()
                        ->integerNode(self::OPTION_IMAGE_QUALITY)->end()
                        ->enumNode(self::OPTION_PAGE_SIZE)->values(self::pageSizes)->end()
                    ->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}