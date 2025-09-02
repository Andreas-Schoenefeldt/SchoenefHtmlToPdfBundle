<?php

namespace Schoenef\HtmlToPdfBundle;

use Schoenef\HtmlToPdfBundle\Service\Html2PdfConnector;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SchoenefHtmlToPdfBundle extends AbstractBundle
{

    public const pageSizes = ['A0', 'A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'A9', 'B0', 'B1', 'B1', 'B2', 'B3', 'B4', 'B5', 'B6', 'B7', 'B8', 'B9', 'C5E', 'Comm10E', 'DLE', 'Executive', 'Folio', 'Ledger', 'Legal', 'Letter', 'Tabloid'];
    public const PROVIDER_PDF_ROCKET = 'pdfrocket';

    public const KEY_PROVIDER = 'provider';
    public const KEY_TIMEOUT = 'timeout';
    public const KEY_APIKEY = 'apikey';
    public const KEY_DEFAULT_OPTIONS = 'default_options';

    public const OPTION_DPI = 'dpi';
    public const OPTION_SHRINKING = 'shrinking';
    public const OPTION_IMAGE_QUALITY = 'image_quality';
    public const OPTION_PAGE_SIZE = 'page_size';
    public const OPTION_ZOOM = 'zoom';
    public const OPTION_JS_DELAY = 'js_delay';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
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
    }


    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        // Register the configuration as a parameter for use in services.yml
        $container->parameters()->set('schoenef_html_to_pdf.config', $config);

        // load an XML, PHP or Yaml file
        $container->import('../config/services.yml');
    }
}