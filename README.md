GroupBy Search API
=======

[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/groupby/api-php.svg)](https://scrutinizer-ci.com/g/groupby/api-php/?branch=develop)
[![Build Status](https://img.shields.io/travis/groupby/api-php.svg)](https://travis-ci.org/groupby/api-php)
[![Packagist Version](https://img.shields.io/packagist/v/groupby/api.svg)](https://packagist.org/packages/groupby/api)
[![Packagist Downloads](https://img.shields.io/packagist/dt/groupby/api.svg)](https://packagist.org/packages/groupby/api)
![license](https://img.shields.io/github/license/groupby/api-php.svg)

### Setup Instructions

For more on robo php taskrunner see [here](http://codegyre.github.io/Robo/).

### To install

    robo install
  
### To test

    robo test

### To add a dependency to this project

#### Composer
Add the following to the `require` block of your composer.json

```json
"groupby/api": "2.0.243"
```

or run

    composer require groupby/api:2.0.243

### Examples

#### Searching

```php
$bridge = new CloudBridge('<client key', 'myCustomerId');
$query = new Query();
$query->setQuery('dvd');
/** @var Results $results */
$results = $bridge->search($query);
```

### [Quickstart Web Application](https://github.com/groupby/quickstart-php)
