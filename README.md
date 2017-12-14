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

This library provide set of cross database doctrine DQL functions. Supported databases are MySQL and PostgreSQL.
Available functions:

* `DATE(expr)` - Extract the date part of a date or datetime expression
* `TIME(expr)` - Extract the time portion of the expression passed
* `TIMESTAMP(expr)` - Convert expression to TIMESTAMP
* `TIMESTAMPDIFF(unit, datetime_expr1, datetime_expr2)` - Returns *datetime_expr2* – *datetime_expr1*, where *datetime_expr1* and *datetime_expr2* are date or datetime expressions. The *unit* should be one of the following values: *MICROSECOND* (microseconds), *SECOND*, *MINUTE*, *HOUR*, *DAY*, *WEEK*, *MONTH*, *QUARTER*, or *YEAR*.
* `CONVERT_TZ(expr, from_tz, to_tz)` - Converts a datetime value expr from the time zone given by from_tz to the time zone given by to_tz and returns the resulting datetime value
* `DAY(expr)` - Return the day of the month (0-31)
* `DAYOFWEEK(expr)` - Returns the weekday index for date (1 = Sunday, 2 = Monday, …, 7 = Saturday). These index values correspond to the ODBC standard.
* `DAYOFMONTH(expr)` - Returns the day of the month for date, in the range 1 to 31, or 0 for dates such as '0000-00-00' or '2008-00-00' that have a zero day part.
* `DAYOFYEAR(expr)` - Return the day of the year (1-366)
* `HOUR(expr)` - Return the hour from the date passed
* `MD5(expr)` - Calculate MD5 checksum
* `MINUTE(expr)` - Return the minute from the date passed
* `MONTH(expr)` - Return the month from the date passed
* `QUARTER(expr)` - Return the quarter from the date passed
* `SECOND(expr)` - Return the second from the date passed
* `WEEK(expr)` - The number of the week of the year that the day is in. By definition (ISO 8601), weeks start on Mondays and the first week of a year contains January 4 of that year. In other words, the first Thursday of a year is in week 1 of that year.
* `YEAR(expr)` - Return the year from the date passed
* `POW(expr, power)` - Return the argument raised to the specified power
* `ROUND(value, precision)` - Return the value formated with the precision specified
* `SIGN(expr)` - Return the sign of the argument
* `CAST(expr as type)` - Takes an expression of any type and produces a result value of a specified type. Supported types are: "char, string, text, date, datetime, time, int, integer, decimal, boolean"
* `CONCAT_WS` - Concatenate all but the first argument with separators. The first argument is used as the separator string.
* `GROUP_CONCAT` - Return a concatenated string
* `REPLACE(subject,from,to)` - Replaces all occurrances of a string "from" with "to" within a string "subject"
* `DATE_FORMAT(date,format)` - Formats the date value according to the format string.
 The following specifiers may be used in the format string. The % character is required before format specifier characters.

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

GROUP_CONCAT full syntax:
```
GROUP_CONCAT([DISTINCT] expr [,expr ...]
            [ORDER BY {unsigned_integer | col_name | expr}
                [ASC | DESC] [,col_name ...]]
            [SEPARATOR str_val])
```

Installation
------------

Add the following dependency to your composer.json
```json
{
    "require": {
        "oro/doctrine-extensions": "dev-master"
    }
}
```
Functions Registration
----------------------

### Doctrine2

[Doctrine2 Documentation: "DQL User Defined Functions"](http://docs.doctrine-project.org/en/latest/cookbook/dql-user-defined-functions.html)

```php
<?php
$config = new \Doctrine\ORM\Configuration();
$config->addCustomStringFunction('group_concat', 'Oro\ORM\Query\AST\Functions\String\GroupConcat');
$config->addCustomNumericFunction('hour', 'Oro\ORM\Query\AST\Functions\SimpleFunction');
$config->addCustomDatetimeFunction('date', 'Oro\ORM\Query\AST\Functions\SimpleFunction');

$em = EntityManager::create($dbParams, $config);
```

### Symfony2

In Symfony2 you can register functions in `config.yml`

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
            string_functions:
                md5:            Oro\ORM\Query\AST\Functions\SimpleFunction
                group_concat:   Oro\ORM\Query\AST\Functions\String\GroupConcat
                concat_ws:      Oro\ORM\Query\AST\Functions\String\ConcatWs
                cast:           Oro\ORM\Query\AST\Functions\Cast
                replace:        Oro\ORM\Query\AST\Functions\String\Replace
                date_format:    Oro\ORM\Query\AST\Functions\String\DateFormat
```

### Silex

If you are using an ORM service provider make sure that you are adding the custom function to the configuration

for [palmasev/DoctrineORMServiceProvider](https://github.com/palmasev/DoctrineORMServiceProvider)

``` php

    $config = $app['doctrine_orm.configuration'];

    $config->addCustomDateTimeFunction( 'year', 'Oro\ORM\Query\AST\Functions\SimpleFunction' );
    $config->addCustomDateTimeFunction( 'month', 'Oro\ORM\Query\AST\Functions\SimpleFunction' );
```

for [dflydev/dflydev-doctrine-orm-service-provider](https://github.com/dflydev/dflydev-doctrine-orm-service-provider)

``` php

    $app->register( new Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider, [
        ...
        'orm.custom.functions.string' => [
            'md5'           => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
            'cast'          => 'Oro\ORM\Query\AST\Functions\Cast',
            'group_concat'  => 'Oro\ORM\Query\AST\Functions\String\GroupConcat',
            'concat_ws'     => 'Oro\ORM\Query\AST\Functions\String\ConcatWs',
            'replace'       => 'Oro\ORM\Query\AST\Functions\String\Replace',
            'date_format'   => 'Oro\ORM\Query\AST\Functions\String\DateFormat'
        ],
        'orm.custom.functions.datetime' => [
            'date'          => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
            'time'          => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
            'timestamp'     => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
            'convert_tz'    => 'Oro\ORM\Query\AST\Functions\DateTime\ConvertTz'
        ],
        'orm.custom.functions.numeric' => [
            'timestampdiff' => 'Oro\ORM\Query\AST\Functions\Numeric\TimestampDiff',
            'dayofyear'     => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
            'dayofweek'     => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
            'week'          => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
            'day'           => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
            'hour'          => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
            'minute'        => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
            'month'         => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
            'quarter'       => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
            'second'        => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
            'year'          => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
            'sign'          => 'Oro\ORM\Query\AST\Functions\Numeric\Sign',
            'pow'           => 'Oro\ORM\Query\AST\Functions\Numeric\Pow',
            'round'         => 'Oro\ORM\Query\AST\Functions\Numeric\Round',
        ]
    ]);
```
### Zend Framework 2

In Zend Framework 2 you can register functions in `config/autoload/doctrine.global.php`

``` php
return [
        'doctrine' => [
        'configuration' => [
            'orm_default' => [
                'datetime_functions' => [
                    'date'          => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'time'          => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'timestamp'     => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'convert_tz'    => 'Oro\ORM\Query\AST\Functions\DateTime\ConvertTz',
                ],
                'numeric_functions' => [
                    'timestampdiff' => 'Oro\ORM\Query\AST\Functions\Numeric\TimestampDiff',
                    'dayofyear'     => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'dayofmonth'    => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'dayofweek'     => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'week'          => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'day'           => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'hour'          => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'minute'        => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'month'         => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'quarter'       => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'second'        => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'year'          => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'sign'          => 'Oro\ORM\Query\AST\Functions\Numeric\Sign',
                    'pow'           => 'Oro\ORM\Query\AST\Functions\Numeric\Pow',
                    'round'         => 'Oro\ORM\Query\AST\Functions\Numeric\Round',
                ],
                'string_functions'  => [
                    'md5'           => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'group_concat'  => 'Oro\ORM\Query\AST\Functions\String\GroupConcat',
                    'cast'          => 'Oro\ORM\Query\AST\Functions\Cast',
                    'concat_ws'     => 'Oro\ORM\Query\AST\Functions\String\ConcatWs',
                    'replace'       => 'Oro\ORM\Query\AST\Functions\String\Replace',
                    'date_format'   => 'Oro\ORM\Query\AST\Functions\String\DateFormat'
                ]
            ]
        ]
    ]
];
```
Extendability and Database Support
----------------------------------

### Architecture

Most of functions, that require only one ArithmeticPrimary argument may be parsed with `Oro\ORM\Query\AST\Functions\SimpleFunction`.
This class is responsible for parsing function definition and saving parsed data to parameters. It is extended from
`Oro\ORM\Query\AST\Functions\AbstractPlatformAwareFunctionNode`. This layer work with DQL function parsing.
SQL generation is responsibility of platform specific functions, that extends `PlatformFunctionNode`.
`AbstractPlatformAwareFunctionNode` creates appropriate instance of platform function based on current connection Database Platform instance name and DQL function name.

Platform function classes naming rule is:

```
Oro\ORM\Query\AST\Platform\Functions\$platformName\$functionName
```

### Adding new platform
To add support of new platform you just need to create new folder `Oro\ORM\Query\AST\Platform\Functions\$platformName`
and implement required function there according to naming rules

### Adding new function

In case when your function is function with only one ArithmeticPrimary argument you may not create DQL function parser
and use `Oro\ORM\Query\AST\Functions\SimpleFunction` for this.
Then only platform specific SQL implementation of your function is required.

In case when your are implementing more complex function, like GROUP_CONCAT both DQL parser and SQL implementations are required.

If you want to add new function to this library feel free to fork it and create pull request with your implementation.
Please, remember to update documentation with your new functions. All new functions MUST be implemented for all platforms.

Field Types
===========

This library also consist additional field types:

* `MoneyType`
* `PercentType`
* `ObjectType`
* `ArrayType`

`ObjectType` and `ArrayType` use base64 encoded string to store values in Db instead of storing serialized strings.
For backward compatibility values that are already stored in Db will be unserialized without base64 encoding. New values
will be base64 encoded before storing in Db and base64 decoded before unserialization.
