# SchoenefHtmlToPdfBundle
A simple bundle to add html 2 pdf service provider in a simple way to your symfony project

## Installation

### Step 1: Download the Bundle


Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require schoenef/html-to-pdf-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Schoenef\HtmlToPdfBundle\SchoenefHtmlToPdfBundle(), // takes care of html to pdf conversion via third party services
        );

        // ...
    }

    // ...
}
```

### Step 3: Configure the Bundle

Add the following configuration to your ```app/config/config.yml```:
```yml
schoenef_html_to_pdf:
  provider: pdfrocket
  timeout: 20
  apikey: "%html_to_pdf_apikey%"
```

And to your ```app/config/parameter.yml```:
```yml
parameters:
  ...
  html_to_pdf_apikey: yourApiKey
```

#### Available configuration options

1. **provider**: default: pdfrocket - no other value available at the moment
1. **timeout**: default: 20 - the timeout in seconds of a single http call
1. **apikey**: The api key you got from your provider to turn html pages into pdf

### Usage

To use the html to pdf connector and save pdf files, you can use the following inside of symfony controllers:

```php
$connector = $this->get('html2pdf.connector');
$connector->saveUrlAsPdf('http://some.url', 'some/file/path.pdf');
```