<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 29/03/17
 * Time: 18:01
 */

namespace Schoenef\HtmlToPdfBundle\Service;


use GuzzleHttp\Client;
use Symfony\Component\Filesystem\Filesystem;

class Html2PdfConnector {

    private $config;

    private $client;

    public static $providerBaseURIs = [
            'pdfrocket' => 'http://api.html2pdfrocket.com'
        ];

    public function __construct(array $conectorConfig){
        $this->config = $conectorConfig;

        // seting the defaults
        if (! $this->config['provider']) $this->config['provider'] = 'pdfrocket';
        if (! $this->config['timeout']) $this->config['timeout'] = 20;


        $this->client = new Client([
            // Base URI is used with relative requests
            'base_uri' => self::$providerBaseURIs[$this->config['provider']],
            // You can set any number of default request options.
            'timeout'  => $this->config['timeout'],
        ]);
    }

    /**
     * @param string $url
     * @param string $filePath if set, the result will be saved to the
     * @return bool
     */
    public function saveUrlToFile($url, $filePath){

        $response = $this->client->request('GET', '/pdf', ['query' => [
            'apikey' => $this->config['apikey'],
            'value' => $url
        ]]);


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



}