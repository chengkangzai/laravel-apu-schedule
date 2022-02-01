# This is my package laravel-apu-schedule

[![Latest Version on Packagist](https://img.shields.io/packagist/v/chengkangzai/laravel-apu-schedule.svg?style=flat-square)](https://packagist.org/packages/chengkangzai/laravel-apu-schedule)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/chengkangzai/laravel-apu-schedule/run-tests?label=tests)](https://github.com/chengkangzai/laravel-apu-schedule/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/chengkangzai/laravel-apu-schedule/Check%20&%20fix%20styling?label=code%20style)](https://github.com/chengkangzai/laravel-apu-schedule/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/chengkangzai/laravel-apu-schedule.svg?style=flat-square)](https://packagist.org/packages/chengkangzai/laravel-apu-schedule)



This laravel package provide simple API to get all and query the student's timetable information from [Asia Pacific University(APU)](https://apu.edu.my/).

## Installation

You can install the package via composer:

```bash
composer require chengkangzai/laravel-apu-schedule
```

## Usage

## Get all the intake code

```php
use Chengkangzai\ApuSchedule\ApuSchedule;

$intakes = ApuSchedule::getIntakes(); // UC3F2111SE, AFCF2011AS ... 
```

## Get all the grouping by intake code

```php
use Chengkangzai\ApuSchedule\ApuSchedule;

ApuSchedule::getGroupings("UC3F2111SE"); // G1,G2,G3
```

## Get schedule of specific intake and grouping

```php
use Chengkangzai\ApuSchedule\ApuSchedule;

ApuSchedule::getSchedule("UC3F2111SE","G1"); 

```

<details><summary>Example Output</summary>

```json
[
    {
        "INTAKE": "...",
        "MODID": "...",
        "MODULE_NAME": "...",
        "DAY": "...",
        "LOCATION": "...",
        "ROOM": "...",
        "LECTID": "...",
        "NAME": "...",
        "SAMACCOUNTNAME": "...",
        "DATESTAMP": "...",
        "DATESTAMP_ISO": "...",
        "TIME_FROM": "...",
        "TIME_TO": "...",
        "TIME_FROM_ISO": "...",
        "TIME_TO_ISO": "...",
        "GROUPING": "...",
        "CLASS_CODE": "...",
        "COLOR": "..."
    },
    {}
]
```

</details>

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [chengkangzai](https://github.com/chengkangzai)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
