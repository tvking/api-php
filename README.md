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
"groupby/api": "2.0.244"
```

or run

    composer require groupby/api:2.0.244

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
Look [here]() for an example of how to load the annotations used by our API for serialization.

#### Changes

As of **v2.0.44** zones in a template will be serialized as an array with the names of the zones as keys.
