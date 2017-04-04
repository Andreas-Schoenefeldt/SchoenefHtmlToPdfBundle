<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 29/03/17
 * Time: 18:01
 */

namespace Schoenef\HtmlToPdfBundle\Service;


use GuzzleHttp\Client;
use Schoenef\HtmlToPdfBundle\DependencyInjection\Configuration;
use Symfony\Component\Filesystem\Filesystem;

class Html2PdfConnector {

    const providerBaseURIs = [
        Configuration::PROVIDER_PDF_ROCKET => 'http://api.html2pdfrocket.com'
    ];

    const providerConfigurationMapping = [
        Configuration::PROVIDER_PDF_ROCKET => [
            Configuration::OPTION_PAGE_SIZE => 'PageSize',
            Configuration::OPTION_SHRINKING => 'DisableShrinking',
            Configuration::OPTION_DPI => 'Dpi',
            Configuration::OPTION_IMAGE_QUALITY => 'ImageQuality',
            Configuration::OPTION_ZOOM => 'Zoom',
            Configuration::OPTION_JS_DELAY => 'JavascriptDelay'
        ]
    ];

    private $config;

    private $client;


    /** @var string */
    private $provider = ''; // the current provider
    /** @var array */
    private $configurationMapping = []; // the mapping of the current provider

    public function __construct(array $connectorConfig){
        $this->config = $connectorConfig;

        $this->provider = $this->config[Configuration::KEY_PROVIDER];
        $this->configurationMapping = self::providerConfigurationMapping[$this->provider];

        $this->client = new Client([
            // Base URI is used with relative requests
            'base_uri' => self::providerBaseURIs[$this->config[Configuration::KEY_PROVIDER]],
            // You can set any number of default request options.
            'timeout'  => $this->config[Configuration::KEY_TIMEOUT],
        ]);
    }

    /**
     * @param string $url
     * @param string $filePath if set, the result will be saved to the
     * @param array $pdfOptions
     * @return bool
     */
    public function saveUrlAsPdf($url, $filePath, $pdfOptions = array()){

        $providerOptions = $this->getRequestOptions($pdfOptions);
        $providerOptions['apikey'] = $this->config[Configuration::KEY_APIKEY];
        $providerOptions['value'] = $url;

        $response = $this->client->request('GET', '/pdf', ['query' => $providerOptions]);
        
        if ($response->getStatusCode() == '200') {

            if ($filePath) {
                $fs = new Filesystem();
                // check if file exists - always regenerate for now
                if ($fs->exists($filePath)) {
                    $fs->remove($filePath);
                }

                $fs->dumpFile($filePath, $response->getBody());
            }

            return true;
        }

        return false;
    }


    private function getRequestOptions($pdfOptions = array()) {

        $finalOptions = array_merge($this->config[Configuration::KEY_DEFAULT_OPTIONS], $pdfOptions);


        // validation
        if (array_key_exists(Configuration::OPTION_PAGE_SIZE, $finalOptions) && ! in_array($finalOptions[Configuration::OPTION_PAGE_SIZE], Configuration::pageSizes)) {
            $value = $finalOptions[Configuration::OPTION_PAGE_SIZE];
            throw new \Exception("$value is not a allowed " . Configuration::OPTION_PAGE_SIZE);
        }

        $providerOptions = [];


        // This is now the mapping to pdfrocket, might have to move into its dedicated class at some point
        switch ($this->provider){
            case Configuration::PROVIDER_PDF_ROCKET:
                foreach ($finalOptions as $key => $value) {

                    if (! array_key_exists($key, $this->configurationMapping)   ) {
                        throw new \Exception("$key is not a valid configuration for $this->provider");
                    }

                    $providerOption = $this->configurationMapping[$key];

                    switch ($key) {
                        default:
                            $providerOptions[$providerOption] = $value;
                            break;
                        case Configuration::OPTION_SHRINKING:
                            if (! $value) {
                                $providerOptions[$providerOption] = 'true';
                            }
                            break;
                    }
                }

                break;
        }

        return $providerOptions;

    }



}