Oro Doctrine Extensions
=======================

[![Build Status](https://travis-ci.org/oroinc/doctrine-extensions.svg)](https://travis-ci.org/oroinc/doctrine-extensions)

Table of Contents
-----------------

- [DQL Functions](#dql-functions-list)
    - [Installation](#installation)
    - [Functions Registration](#functions-registration)
        - [Doctrine2](#doctrine2)
        - [Symfony2](#symfony2)
        - [Silex](#silex)
        - [Zend Framework 2](#zend-framework-2)
    - [Extendability and Database Support](#extendability-and-database-support)
        - [Architecture](#architecture)
        - [Adding new platform](#adding-new-platform)
        - [Adding new function](#adding-new-function)
- [Field Types](#field-types)

DQL Functions
=============

This library provides a set of Doctrine DQL functions for MySQL and PostgreSQL.

Available functions:

* `DATE(expr)` - Extracts the date part of a date or datetime expression.
* `TIME(expr)` - Extracts the time portion of the provided expression.
* `TIMESTAMP(expr)` - Converts an expression to TIMESTAMP.
* `TIMESTAMPDIFF(unit, datetime_expr1, datetime_expr2)` - Returns *datetime_expr2* – *datetime_expr1*, where *datetime_expr1* and *datetime_expr2* are date or datetime expressions. The `unit` parameter can take one of the following values: *MICROSECOND* (microseconds), *SECOND*, *MINUTE*, *HOUR*, *DAY*, *WEEK*, *MONTH*, *QUARTER*, or *YEAR*.
* `CONVERT_TZ(expr, from_tz, to_tz)` - Converts a datetime value expr from the time zone given by `from_tz` to the time zone given by `to_tz` and returns the resulting datetime value.
* `DAY(expr)` - Returns the day of the month (0-31).
* `DAYOFWEEK(expr)` - Returns the weekday index (1 = Sunday, 2 = Monday, …, 7 = Saturday) for a date expression. These index values correspond to the ODBC standard.
* `DAYOFMONTH(expr)` - Returns the day of the month for a date expression, in the range 1 to 31, or 0 for dates such as '0000-00-00' or '2008-00-00' that have a zero day part.
* `DAYOFYEAR(expr)` - Returns the day of the year (1-366).
* `HOUR(expr)` - Returns the hour from the argument.
* `MD5(expr)` - Calculates MD5 checksum.
* `MINUTE(expr)` - Returns the minute from the argument.
* `MONTH(expr)` - Returns the month from the date passed.
* `QUARTER(expr)` - Returns the quarter from the date passed.
* `SECOND(expr)` - Returns the second from the argument.
* `WEEK(expr)` - Returns the week number of the year that the day is in. By ISO 8601, weeks start on Monday and the first week of a year contains January 4 of that year. In other words, the first Thursday of a year is in week 1 of that year.
* `YEAR(expr)` - Returns the year from the date passed.
* `POW(expr, power)` - Returns the argument raised to the specified power.
* `ROUND(value, ?precision)` - Rounds the value to the specified precision (defaults to 0 precision if not specified).
* `CEIL(value)` - Returns the value rounded up.
* `SIGN(expr)` - Returns the sign of the argument.
* `CAST(expr as type)` - Takes an expression of any type and produces a result value of a specified type. Supported types are: `char`, `string`, `text`, `date`, `datetime`, `time`, `int`, `integer`, `bigint`, `decimal`, `boolean`, `binary`.
* `CONCAT_WS` - Concatenate all but the first argument. The first argument is used as the separator string.
* `GROUP_CONCAT` - Returns a concatenated string. GROUP_CONCAT full syntax:
```
GROUP_CONCAT([DISTINCT] expr [,expr ...]
            [ORDER BY {unsigned_integer | col_name | expr}
                [ASC | DESC] [,col_name ...]]
            [SEPARATOR str_val])
```

* `REPLACE(subject, from, to)` - Replaces all occurrences of a string `from` with `to` within a string `subject`.
* `DATE_FORMAT(date, format)` - Formats the date value according to the format string.
 The following specifiers may be used in the format string (the `%` character is required before format specifier characters):

|Specifier|Description|
| ------------- | -----|
|%a|Abbreviated weekday name (Sun..Sat)|
|%b|Abbreviated month name (Jan..Dec)|
|%c|Month, numeric (0..12)|
|%d|Day of the month, numeric (00..31)|
|%e|Day of the month, numeric (0..31)|
|%f|Microseconds (000000..999999)|
|%H|Hour (00..23)|
|%h|Hour (01..12)|
|%I|Hour (01..12)|
|%i|Minutes, numeric (00..59)|
|%j|Day of year (001..366)|
|%k|Hour (0..23)|
|%l|Hour (1..12)|
|%M|Month name (January..December)|
|%m|Month, numeric (00..12)|
|%p|AM or PM|
|%r|Time, 12-hour (hh:mm:ss followed by AM or PM)|
|%S|Seconds (00..59)|
|%s|Seconds (00..59)|
|%T|Time, 24-hour (hh:mm:ss)|
|%W|Weekday name (Sunday..Saturday)|
|%Y|Year, numeric, four digits|
|%y|Year, numeric (two digits)|
|%%|A literal % character|

Installation
------------

Add the following dependency to your composer.json:
```json
{
    "require": {
        "oro/doctrine-extensions": "^2.0"
    }
}
```

Registering Functions
---------------------

### Doctrine2

[Doctrine2 Documentation: "DQL User Defined Functions"](https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/cookbook/dql-user-defined-functions.html)

```php
<?php
$config = new \Doctrine\ORM\Configuration();
$config->addCustomStringFunction('group_concat', 'Oro\ORM\Query\AST\Functions\String\GroupConcat');
$config->addCustomNumericFunction('hour', 'Oro\ORM\Query\AST\Functions\SimpleFunction');
$config->addCustomDatetimeFunction('date', 'Oro\ORM\Query\AST\Functions\SimpleFunction');

$em = EntityManager::create($dbParams, $config);
```

### Symfony

In Symfony you can register functions in `config.yml`

```yaml
doctrine:
    orm:
        dql:
            datetime_functions:
                date:           Oro\ORM\Query\AST\Functions\SimpleFunction
                time:           Oro\ORM\Query\AST\Functions\SimpleFunction
                timestamp:      Oro\ORM\Query\AST\Functions\SimpleFunction
                convert_tz:     Oro\ORM\Query\AST\Functions\DateTime\ConvertTz
            numeric_functions:
                timestampdiff:  Oro\ORM\Query\AST\Functions\Numeric\TimestampDiff
                dayofyear:      Oro\ORM\Query\AST\Functions\SimpleFunction
                dayofmonth:     Oro\ORM\Query\AST\Functions\SimpleFunction
                dayofweek:      Oro\ORM\Query\AST\Functions\SimpleFunction
                week:           Oro\ORM\Query\AST\Functions\SimpleFunction
                day:            Oro\ORM\Query\AST\Functions\SimpleFunction
                hour:           Oro\ORM\Query\AST\Functions\SimpleFunction
                minute:         Oro\ORM\Query\AST\Functions\SimpleFunction
                month:          Oro\ORM\Query\AST\Functions\SimpleFunction
                quarter:        Oro\ORM\Query\AST\Functions\SimpleFunction
                second:         Oro\ORM\Query\AST\Functions\SimpleFunction
                year:           Oro\ORM\Query\AST\Functions\SimpleFunction
                sign:           Oro\ORM\Query\AST\Functions\Numeric\Sign
                pow:            Oro\ORM\Query\AST\Functions\Numeric\Pow
                round:          Oro\ORM\Query\AST\Functions\Numeric\Round
                ceil:           Oro\ORM\Query\AST\Functions\SimpleFunction
            string_functions:
                md5:            Oro\ORM\Query\AST\Functions\SimpleFunction
                group_concat:   Oro\ORM\Query\AST\Functions\String\GroupConcat
                concat_ws:      Oro\ORM\Query\AST\Functions\String\ConcatWs
                cast:           Oro\ORM\Query\AST\Functions\Cast
                replace:        Oro\ORM\Query\AST\Functions\String\Replace
                date_format:    Oro\ORM\Query\AST\Functions\String\DateFormat
```

### Laminas Project

In Laminas Project (with DoctrineORMModule) you can register functions in `config/autoload/doctrine.global.php`

``` php
return [
    'doctrine' => [
        'configuration' => [
            'orm_default' => [
                'datetime_functions' => [
                    'date'          => \Oro\ORM\Query\AST\Functions\SimpleFunction::class,
                    'time'          => \Oro\ORM\Query\AST\Functions\SimpleFunction::class,
                    'timestamp'     => \Oro\ORM\Query\AST\Functions\SimpleFunction::class,
                    'convert_tz'    => \Oro\ORM\Query\AST\Functions\DateTime\ConvertTz::class,
                ],
                'numeric_functions' => [
                    'timestampdiff' => \Oro\ORM\Query\AST\Functions\Numeric\TimestampDiff::class,
                    'dayofyear'     => \Oro\ORM\Query\AST\Functions\SimpleFunction::class,
                    'dayofmonth'    => \Oro\ORM\Query\AST\Functions\SimpleFunction::class,
                    'dayofweek'     => \Oro\ORM\Query\AST\Functions\SimpleFunction::class,
                    'week'          => \Oro\ORM\Query\AST\Functions\SimpleFunction::class,
                    'day'           => \Oro\ORM\Query\AST\Functions\SimpleFunction::class,
                    'hour'          => \Oro\ORM\Query\AST\Functions\SimpleFunction::class,
                    'minute'        => \Oro\ORM\Query\AST\Functions\SimpleFunction::class,
                    'month'         => \Oro\ORM\Query\AST\Functions\SimpleFunction::class,
                    'quarter'       => \Oro\ORM\Query\AST\Functions\SimpleFunction::class,
                    'second'        => \Oro\ORM\Query\AST\Functions\SimpleFunction::class,
                    'year'          => \Oro\ORM\Query\AST\Functions\SimpleFunction::class,
                    'sign'          => \Oro\ORM\Query\AST\Functions\Numeric\Sign::class,
                    'pow'           => \Oro\ORM\Query\AST\Functions\Numeric\Pow::class,
                    'round'         => \Oro\ORM\Query\AST\Functions\Numeric\Round::class,
                    'ceil'          => \Oro\ORM\Query\AST\Functions\SimpleFunction::class,
                ],
                'string_functions'  => [
                    'md5'           => \Oro\ORM\Query\AST\Functions\SimpleFunction::class,
                    'group_concat'  => \Oro\ORM\Query\AST\Functions\String\GroupConcat::class,
                    'cast'          => \Oro\ORM\Query\AST\Functions\Cast::class,
                    'concat_ws'     => \Oro\ORM\Query\AST\Functions\String\ConcatWs::class,
                    'replace'       => \Oro\ORM\Query\AST\Functions\String\Replace::class,
                    'date_format'   => \Oro\ORM\Query\AST\Functions\String\DateFormat::class
                ]
            ]
        ]
    ]
];
```

Contributing
------------

### Architecture

#### DQL Function Parsing

Most of that functions that require only one ArithmeticPrimary argument may be parsed with `Oro\ORM\Query\AST\Functions\SimpleFunction`. This class is responsible for parsing a function definition and saving the parsed data to the parameters. It extends `Oro\ORM\Query\AST\Functions\AbstractPlatformAwareFunctionNode`.

#### SQL Generation

SQL generation is the responsibility of the platform-specific functions that extend `PlatformFunctionNode`.
`AbstractPlatformAwareFunctionNode` creates an appropriate instance of a platform function based on the database platform instance name used in the current connection, and the DQL function name.

The naming rule for the platform function classes:
```
Oro\ORM\Query\AST\Platform\Functions\$platformName\$functionName
```

### Adding a New Platform
To add support of a new platform you just need to create a new folder `Oro\ORM\Query\AST\Platform\Functions\$platformName` and add implementations of all the required functions there (using the naming rules as specified above).

### Adding a New Function

In case when your function is a function with only one ArithmeticPrimary argument you do not need a custom DQL function parser and may use `Oro\ORM\Query\AST\Functions\SimpleFunction` for this. Then only the platform specific SQL implementation of your function is required.

In case when you are implementing more complex functions like GROUP_CONCAT, both the DQL parser and the SQL implementations are required.

If you want to add a new function to this library feel free to fork it and create a pull request with your implementation. Please remember to update the documentation (this README.md file) with your new function description and add the necessary tests (and/or test fixtures). All new functions MUST be implemented for both supported platforms (MySQL and PostgreSQL).

Field Types
===========

This library also provides the following field types:

* `MoneyType`
* `PercentType`
* `ObjectType`
* `ArrayType`

`ObjectType` and `ArrayType` use Base64 encoded strings to store values in the database instead of storing serialized strings. For backward compatibility the values that are already stored in the database will be unserialized without Base64 encoding. The new values will be Base64 encoded before saving to the database and Base64 decoded before unserialization.
