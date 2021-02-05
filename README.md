# Easily manage your cache for laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/natsumework/redis-autocomplete.svg?style=flat-square)](https://packagist.org/packages/natsumework/redis-autocomplete)
![Test Status](https://img.shields.io/github/workflow/status/natsumework/redis-autocomplete/run-tests?label=tests&style=flat-square)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/natsumework/redis-autocomplete.svg?style=flat-square)](https://packagist.org/packages/natsumework/redis-autocomplete)

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

TODO

## Usage

TODO

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
