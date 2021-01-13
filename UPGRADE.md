# Upgrade to 2.0

Version 2.0 requires PHP 7.3 or newer and works with Doctrine/ORM 2.6 or newer.

If you just registered the functions and types provided by this library with Doctrine then most likely you do not need to make any changes in your code.

If you have created your own custom functions or types by extending any classes from this library, or if you work with these functions and types directly from your PHP code, then you may need to update your code where necessary as the following methods have changed their signatures:

* `ArrayType#isSerialized(): bool`
* `ArrayType#isSerialized(string $string): bool`
* `ObjectType#isSerialized(string $string): bool`
* `FunctionFactory::create(string $platformName, string $functionName, array $parameters): PlatformFunctionNode`
* `Cast#checkType($type)` has been replaced with `isSupportedType(string $type): bool`
* `TimestampDiff#checkUnit($unit)` has been replaced with `isSupportedUnit(string $unit): bool`
* `AbstractTimestampAwarePlatformFunctionNode#getTimestampValue($expression, SqlWalker $sqlWalker): string`
* `Cast#getSql(SqlWalker $sqlWalker): string`
* `Ceil#getSql(SqlWalker $sqlWalker): string`
* `ConcatWs#getSql(SqlWalker $sqlWalker): string`
* `ConvertTz#getSql(SqlWalker $sqlWalker): string`
* `Date#getSql(SqlWalker $sqlWalker): string`
* `DateFormat#getPostgresFormat(string $format): string`
* `DateFormat#getSql(SqlWalker $sqlWalker): string`
* `Day#getSql(SqlWalker $sqlWalker): string`
* `Dayofmonth#getSql(SqlWalker $sqlWalker): string`
* `Dayofweek#getSql(SqlWalker $sqlWalker): string`
* `Dayofyear#getSql(SqlWalker $sqlWalker): string`
* `GroupConcat#getSql(SqlWalker $sqlWalker): string`
* `Hour#getSql(SqlWalker $sqlWalker): string`
* `Md5#getSql(SqlWalker $sqlWalker): string`
* `Minute#getSql(SqlWalker $sqlWalker): string`
* `Month#getSql(SqlWalker $sqlWalker): string`
* `PlatformFunctionNode#getExpressionValue($expression, SqlWalker $sqlWalker): string`
* `PlatformFunctionNode#getSql(SqlWalker $sqlWalker): string;`
* `Pow#getSql(SqlWalker $sqlWalker): string`
* `Quarter#getSql(SqlWalker $sqlWalker): string`
* `Replace#getSql(SqlWalker $sqlWalker): string`
* `Round#getSql(SqlWalker $sqlWalker): string`
* `Second#getSql(SqlWalker $sqlWalker): string`
* `Sign#getSql(SqlWalker $sqlWalker): string`
* `Time#getSql(SqlWalker $sqlWalker): string`
* `Timestamp#getSql(SqlWalker $sqlWalker): string`
* `Timestampdiff#getAge(string $firstDateTimestamp, string $secondDateTimestamp): string`
* `Timestampdiff#getDiffForDay(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker): string`
* `Timestampdiff#getDiffForHour(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker): string`
* `Timestampdiff#getDiffForMicrosecond(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker): string`
* `Timestampdiff#getDiffForMinute(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker): string`
* `Timestampdiff#getDiffForMonth(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker): string`
* `Timestampdiff#getDiffForQuarter(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker): string`
* `Timestampdiff#getDiffForSecond(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker): string`
* `Timestampdiff#getDiffForWeek(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker): string`
* `Timestampdiff#getDiffForYear(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker): string`
* `Timestampdiff#getFloorValue(string $value): string`
* `Timestampdiff#getRawDiffForSecond(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker): string`
* `Timestampdiff#getSql(SqlWalker $sqlWalker): string`
* `Timestampdiff#getSqlByUnit( string $unit, Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker ): string`
* `Week#getSql(SqlWalker $sqlWalker): string`
* `Year#getSql(SqlWalker $sqlWalker): string`
