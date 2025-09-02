<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 29/03/17
 * Time: 18:01
 */

namespace Schoenef\HtmlToPdfBundle\Service;


use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Schoenef\HtmlToPdfBundle\SchoenefHtmlToPdfBundle;
use Symfony\Component\Filesystem\Filesystem;

class Html2PdfConnector {

    const providerBaseURIs = [
        SchoenefHtmlToPdfBundle::PROVIDER_PDF_ROCKET => 'https://api.html2pdfrocket.com'
    ];

    const providerSchoenefHtmlToPdfBundleMapping = [
        SchoenefHtmlToPdfBundle::PROVIDER_PDF_ROCKET => [
            SchoenefHtmlToPdfBundle::OPTION_PAGE_SIZE => 'PageSize',
            SchoenefHtmlToPdfBundle::OPTION_SHRINKING => 'DisableShrinking',
            SchoenefHtmlToPdfBundle::OPTION_DPI => 'Dpi',
            SchoenefHtmlToPdfBundle::OPTION_IMAGE_QUALITY => 'ImageQuality',
            SchoenefHtmlToPdfBundle::OPTION_ZOOM => 'Zoom',
            SchoenefHtmlToPdfBundle::OPTION_JS_DELAY => 'JavascriptDelay'
        ]
    ];

    private $config;

    private $client;

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $provider = ''; // the current provider
    /** @var array */
    private $SchoenefHtmlToPdfBundleMapping = []; // the mapping of the current provider

    public function __construct(array $connectorConfig, LoggerInterface $logger = null){
        $this->config = $connectorConfig;
        $this->logger = $logger ?: new NullLogger();

        $this->provider = $this->config[SchoenefHtmlToPdfBundle::KEY_PROVIDER];
        $this->SchoenefHtmlToPdfBundleMapping = self::providerSchoenefHtmlToPdfBundleMapping[$this->provider];

        $this->client = new Client([
            // Base URI is used with relative requests
            'base_uri' => self::providerBaseURIs[$this->config[SchoenefHtmlToPdfBundle::KEY_PROVIDER]],
            // You can set any number of default request options.
            'timeout'  => $this->config[SchoenefHtmlToPdfBundle::KEY_TIMEOUT],
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
        $providerOptions['apikey'] = $this->config[SchoenefHtmlToPdfBundle::KEY_APIKEY];
        $providerOptions['value'] = $url;

        try {

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
            } else {
                $this->logger->warning('PDF generation failed with non-200 status code', [
                    'url' => $url,
                    'status_code' => $response->getStatusCode(),
                    'provider' => $this->provider
                ]);
            }
        } catch (\Exception $e) {
            $this->logger->error('Exception occurred during PDF generation', [
                'url' => $url,
                'provider' => $this->provider,
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }

        return false;
    }


    private function getRequestOptions($pdfOptions = array()) {

        $finalOptions = array_merge($this->config[SchoenefHtmlToPdfBundle::KEY_DEFAULT_OPTIONS], $pdfOptions);


        // validation
        if (array_key_exists(SchoenefHtmlToPdfBundle::OPTION_PAGE_SIZE, $finalOptions) && ! in_array($finalOptions[SchoenefHtmlToPdfBundle::OPTION_PAGE_SIZE], SchoenefHtmlToPdfBundle::pageSizes)) {
            $value = $finalOptions[SchoenefHtmlToPdfBundle::OPTION_PAGE_SIZE];
            $this->logger->error('Invalid page size provided', [
                'provided_value' => $value,
                'allowed_values' => SchoenefHtmlToPdfBundle::pageSizes
            ]);
            throw new \Exception("$value is not a allowed " . SchoenefHtmlToPdfBundle::OPTION_PAGE_SIZE);
        }

        $providerOptions = [];


        // This is now the mapping to pdfrocket, might have to move into its dedicated class at some point
        switch ($this->provider){
            case SchoenefHtmlToPdfBundle::PROVIDER_PDF_ROCKET:
                foreach ($finalOptions as $key => $value) {

                    if (! array_key_exists($key, $this->SchoenefHtmlToPdfBundleMapping)   ) {
                        $this->logger->error('Invalid option provided for provider', [
                            'option' => $key,
                            'provider' => $this->provider,
                            'valid_options' => array_keys($this->SchoenefHtmlToPdfBundleMapping)
                        ]);
                        throw new \Exception("$key is not a valid SchoenefHtmlToPdfBundle for $this->provider");
                    }

                    $providerOption = $this->SchoenefHtmlToPdfBundleMapping[$key];

                    switch ($key) {
                        default:
                            $providerOptions[$providerOption] = $value;
                            break;
                        case SchoenefHtmlToPdfBundle::OPTION_SHRINKING:
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