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

    private $config;

    private $client;

    const providerBaseURIs = [
            'pdfrocket' => 'http://api.html2pdfrocket.com'
        ];

    public function __construct(array $conectorConfig){
        $this->config = $conectorConfig;

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
            throw new \Exception("$value is not a allowed " . Configuration::OPTION_PAGE_SIZE);
        }

        $providerOptions = [];


        // This is now the mapping to pdfrocket, might have to move into its dedicated class at some point
        switch ($this->config[Configuration::KEY_PROVIDER]){
            case Configuration::PROVIDER_PDFROCKET:
                foreach ($finalOptions as $key => $value) {
                    switch ($key) {
                        case Configuration::OPTION_PAGE_SIZE:
                            $providerOptions['PageSize'] = $value;
                            break;
                        case Configuration::OPTION_SHRINKING:
                            if (! $value) {
                                $providerOptions['DisableShrinking'] = 'true';
                            }
                            break;
                        case Configuration::OPTION_DPI:
                            $providerOptions['Dpi'] = $value;
                            break;
                        case Configuration::OPTION_IMAGE_QUALITY:
                            $providerOptions['ImageQuality'] = $value;
                            break;
                    }
                }

                break;
        }

        return $providerOptions;

    }



}