# Meritoo Flash Bundle

Mechanisms, extensions and resources related to Symfony Flash Messages (https://symfony.com/doc/current/controller.html#flash-messages)

[![PHP Version](https://img.shields.io/badge/php-%5E7.2-blue.svg?style=flat-square)](https://img.shields.io/badge/php-%5E7.2-blue.svg)
[![Build Status](https://travis-ci.com/meritoo/flash-bundle.svg?branch=master&style=flat-square)](https://travis-ci.com/meritoo/flash-bundle)
[![Packagist](https://img.shields.io/packagist/v/meritoo/flash-bundle.svg?style=flat-square)](https://packagist.org/packages/meritoo/flash-bundle)
[![license](https://img.shields.io/github/license/meritoo/flash-bundle.svg?style=flat-square)](https://github.com/meritoo/flash-bundle)
[![GitHub commits](https://img.shields.io/github/commits-since/meritoo/flash-bundle/0.1.0.svg?style=flat-square)](https://github.com/meritoo/flash-bundle)
[![Coverage Status](https://coveralls.io/repos/github/meritoo/flash-bundle/badge.svg?branch=master&style=flat-square)](https://coveralls.io/github/meritoo/flash-bundle?branch=master)

# Installation

Run [Composer](https://getcomposer.org) to install this package in your project:

```bash
composer require meritoo/flash-bundle
```

> [How to install Composer?](https://getcomposer.org/download)

# Configuration

All parameters have default values. After installation of this bundle, you have to do nothing. If you want to tweak 
some of parameters, create proper configuration file and enter desired parameters.

Example:

```yaml
# config/packages/meritoo_flash.yaml

meritoo_flash:
    css_classes:
        container: middle-container flash-messages
```

[Read more](docs/Configuration.md)

# Development

More information [you can find here](docs/Development.md)
