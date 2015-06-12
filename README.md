GroupBy Search API
=======

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/groupby/api-php/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/groupby/api-php/?branch=develop) [![Build Status](https://travis-ci.org/groupby/api-php.png)](https://travis-ci.org/groupby/api-php)

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
"groupby/api": "2.0.130"
```

or run

    composer require groupby/api:2.0.130

#### [On Packagist](https://packagist.org/packages/groupby/api)

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
