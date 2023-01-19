# SchoenefHtmlToPdfBundle
A simple bundle to add html 2 pdf service provider in a simple way to your symfony project. Currently, it supports:

 - [html2pdfrocket.com](https://www.html2pdfrocket.com)

## Installation

### Step 1: Download the Bundle


Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require schoenef/html-to-pdf-bundle:~2.0
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, if not happening automatically, enable the bundle by adding it to the list of registered bundles
in the `cofig/bundles.php` file of your project:

```php
<?php
// cofig/bundles.php

// ...
return [
  // ...
  Schoenef\HtmlToPdfBundle\SchoenefHtmlToPdfBundle::class => ['all' => true],
  // ...
]
```

### Step 3: Configure the Bundle

Add the following configuration to your ```config/packages/schoenef_html_to_pdf.yaml```:
```yml
schoenef_html_to_pdf:
  provider: pdfrocket
  timeout: 20
  apikey: "%html_to_pdf_apikey%"
  default_options:
     shrinking: false
     dpi: 300
     image_quality: 100
     page_size: A4
     zoom: 1.2
     js_delay: 500
```

And to your ```.env```:
```yml
HTML_TO_PDF_API_KEY=change_me
```

#### Available configuration options

1. **provider**: default: pdfrocket - no other value available at the moment
1. **timeout**: default: 20 - the timeout in seconds of a single http call
1. **apikey**: The api key you got from your provider to turn html pages into pdf
1. **default_options**: mapping of [pdf options](https://www.html2pdfrocket.com/#htmltopdf)
   1. *shrinking* | (boolean) - if set to false, smart-shrinking is dissabled
   1. *dpi* | (integer) - allows to set the dpi
   1. *image_quality* | (integer) - allows to define the image quality
   1. *page_size* | (enum) - allows to define the pdf page size - ```A0 A1 A2 A3 A4 A5 A6 A7 A8 A9 B0 B1 B1 B2 B3 B4 B5 B6 B7 B8 B9 C5E Comm10E DLE Executive Folio Ledger Legal Letter Tabloid```
   1. *zoom* | (float) - a float number, to zoom the page 
   1. *js_delay* | (integer) - render delay in milliseconds - good to allow the load of external fonts are ajax requests to finish

### Usage

Inject the Service then in the __construct of cour controller or service.

```php
<?php

namespace App\Service;

use Schoenef\HtmlToPdfBundle\Service\Html2PdfConnector;

class MyService {

   public function __construct(
       private readonly Html2PdfConnector $html2PdfConnector,
   ) {}
   
   public function toPdf () {
       $this->html2PdfConnector->saveUrlAsPdf('http://some.url', 'some/file/path.pdf', ['dpi' => 96]);
   }

}
```
