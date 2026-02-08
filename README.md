Log Obfuscator
==============

[![Latest Stable Version](https://poser.pugx.org/roslov/log-obfuscator/v)](https://packagist.org/packages/roslov/log-obfuscator)
[![Total Downloads](https://poser.pugx.org/roslov/log-obfuscator/downloads)](https://packagist.org/packages/roslov/log-obfuscator)
[![License](https://poser.pugx.org/roslov/log-obfuscator/license)](https://packagist.org/packages/roslov/log-obfuscator)
[![PHP Version Require](https://poser.pugx.org/roslov/log-obfuscator/require/php)](https://packagist.org/packages/roslov/log-obfuscator)

This package hides sensitive information in text (usually, in logs).


## Requirements

- PHP 7.4 or higher.


## Installation

The package could be installed with composer:

```shell
composer require roslov/log-obfuscator
```


## General usage

```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

use Roslov\LogObfuscator\LogObfuscator;

require_once __DIR__ . '/vendor/autoload.php';

$obfuscator = new LogObfuscator();

$json = <<<'JSON'
    {
        "username": "user",
        "password": "123456789"
    }
    JSON;

echo $obfuscator->obfuscate($json);
```

This will return:

```
{
    "username": "user",
    "password": "×××××"
}
```


## Testing

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Code style analysis

The code style is analyzed with [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) and
[PSR-12 Ext coding standard](https://github.com/roslov/psr12ext). To run code style analysis:

```shell
./vendor/bin/phpcs --extensions=php --colors --standard=PSR12Ext --ignore=vendor/* -p -s .
```

