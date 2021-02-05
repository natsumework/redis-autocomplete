# Redis Autocomplete for laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/natsumework/redis-autocomplete.svg?style=flat-square)](https://packagist.org/packages/natsumework/redis-autocomplete)
![Test Status](https://img.shields.io/github/workflow/status/natsumework/redis-autocomplete/run-tests?label=tests&style=flat-square)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/natsumework/redis-autocomplete.svg?style=flat-square)](https://packagist.org/packages/natsumework/redis-autocomplete)

Provide an easy way for your laravel app to use redis to speed up autocomplete queries.

## Contents

- [Installation](#installation)
- [Configuration](#configuration)   
- [Usage](#usage)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

Install this package via composer:

```
composer require natsumework/redis-autocomplete
```

Publish the configuration file to `config/redis-autocomplete.php`:

```
php artisan vendor:publish --provider="Natsumework\RedisAutocomplete\AutoCompleteServiceProvider"
```

## Configuration

`config/redis-autocomplete.php`

```
return [
    /*
    |--------------------------------------------------------------------------
    | Redis Connection
    |--------------------------------------------------------------------------
    |
    | Your application's config/database.php configuration file allows you
    | to define multiple Redis connections / servers.
    | Here you may specify the connection you want to use.
    |
    | To specify an instance of the default Redis connection,
    | you may set the value to null.
    */
    'connection' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing a RAM based store such as APC or Memcached, there might
    | be other applications utilizing the same cache. So, we'll specify a
    | value to get prefixed to all our keys so we can avoid collisions.
    |
    */
    'prefix' => 'redis-autocomplete',

    /*
    |--------------------------------------------------------------------------
    | Storage time
    |--------------------------------------------------------------------------
    |
    | Specifies that phrases will expire in a few seconds.
    | If set to null, phrases will be stored indefinitely.
    |
    */
    'ttl' => 60 * 60 * 24
];
```

## Usage

#### Add phrases

Note:
+ The phrase must contain `id` and `name` as attributes
+  phrase`id` must be unique
+ When searching, it will be searched by phrase `name`
+ You may specify the `score` attribute of phrase to sort the phrases, 
and the search results will be retrieved in descending order of score.  
If the score is not specified, the default will be 0
+ You can add any other attributes to the pharse

```
$phrases = [
    [
        'id' => 1, // required
        'name' => 'laravel' // required
    ],
    [
        'id' => 2,
        'name' => 'redis autocomplete',
        'score' => 10, // default is 0
        'custom_column_1' => 'column 1',
        'custom_column_2' => ['item 1', 'item 2'],
        ...
    ]
];

// addPhrases(string $name, $phrases)
Autocomplete::addPhrases('my-phrases', $phrases);
```

You may also use [collections](https://laravel.com/docs/master/collections)

```
$phrases = collect([
    [
        'id' => 1,
        'name' => 'laravel'
    ],
    [
        'id' => 2,
        'name' => 'redis autocomplete'
    ]
]);

Autocomplete::addPhrases('my-phrases-collection', $phrases);
```

or use [Eloquent Collections](https://laravel.com/docs/master/eloquent-collections)

```
// users must contain `id` and `name` as attributes
$phrases = User::all();

Autocomplete::addPhrases('my-users', $phrases);
```

#### Search

```
// array search(string $name, string $keyword, int $limit = 10)
$result = Autocomplete::search('my-phrases', 'keyword', 10);
```

#### Remove phrase

Note that the `id` and `name` of the phrase must be the same as when it was added 

```
// removePhrase(string $name, $phrase)

$phrase = [
    'id' => 1,
    'name' => 'laravel'
]

Autocomplete::removePhrase('my-phrases', $phrase)
```

#### Connection

You may obtain a connection to a specific Redis connection using the Autocomplete facade's connection method

```
Autocomplete::connection('autocomplete')
    ->addPhrases($name, $phrases);

Autocomplete::connection('autocomplete')
    ->search($name, $keyword);
```

#### Ttl

You may obtain a ttl to a specific expiration using the Autocomplete facade's ttl method

```
Autocomplete::ttl(60 * 60)
    ->addPhrases($name, $phrases);
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email natsumework0902@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [natsumework](https://github.com/natsumework)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
