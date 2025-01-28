# ArrayObjectExtended

[![Latest Version on Packagist](https://img.shields.io/packagist/v/code-distortion/array-object-extended.svg?style=flat-square)](https://packagist.org/packages/code-distortion/array-object-extended)
![PHP Version](https://img.shields.io/badge/PHP-8.2%20to%208.4-blue?style=flat-square)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/code-distortion/array-object-extended/run-tests.yml?branch=main&style=flat-square)](https://github.com/code-distortion/array-object-extended/actions)
[![Buy The World a Tree](https://img.shields.io/badge/treeware-%F0%9F%8C%B3-lightgreen?style=flat-square)](https://plant.treeware.earth/code-distortion/array-object-extended)
[![Contributor Covenant](https://img.shields.io/badge/contributor%20covenant-v2.1%20adopted-ff69b4.svg?style=flat-square)](.github/CODE_OF_CONDUCT.md)

***code-distortion/array-object-extended*** provides a substitute ArrayObject class that includes the methods missing from PHP's ArrayObject.



## Introduction

PHP has many [array functions](https://www.php.net/manual/en/ref.array.php) to manipulate arrays. It also has an [ArrayObject](https://www.php.net/manual/en/class.arrayobject.php) which is a class that acts like an array.

However, most of the regular array functions aren't implemented by `ArrayObject`.

This package offers `ArrayObjectExtended` which extends from PHP's `ArrayObject`, and adds many of the missing methods in.

`ArrayObjectExtended` is ***not*** intended to:
- *improve* the methods or functionality,
- be complete (it adds methods where it makes sense),
- be a Collection class, or
- provide a fluent interface.

It ***is*** designed to be faithful to PHP's original method names, parameter usage and return types.

In almost all cases, the only difference between the new class methods and the original is that the `$array` or `$haystack` parameter is removed (because the object inherently contains the array, so it doesn't need to be passed).



## Installation

Install the package via composer:

``` bash
composer require code-distortion/array-object-extended
```



## Usage

`ArrayObjectExtended` acts like PHP's `ArrayObject`. Instantiate and start using it like normal. e.g.

``` php
use CodeDistortion\ArrayObject\ArrayObjectExtended

$myArrayObject = new ArrayObjectExtended(['a', 'b', 'c']);

$myArrayObject->rsort();

foreach ($myArrayObject as $value) {
    print "$value\n";
}
```

You can extend from `ArrayObjectExtended` to add in your own functionality.

``` php
use ArrayAccess;
use CodeDistortion\ArrayObject\ArrayObjectExtended;
use Countable;
use IteratorAggregate;
use Serializable;

/**
 * @template TKey of integer|string
 * @template TValue of string
 * @template-implements IteratorAggregate<TKey, TValue>
 * @template-implements ArrayAccess<TKey, TValue>
 */
class MyArrayObject extends ArrayObjectExtended implements IteratorAggregate, ArrayAccess, Serializable, Countable
{
    // …
}
```



## Available Methods

``` php
append(mixed $value): void
aRSort(int $flags = SORT_REGULAR): bool
aSort(int $flags = SORT_REGULAR): bool
changeKeyCase(int $case = CASE_LOWER): bool
chunk(int $length, bool $preserveKeys = false): array
column(string|int|null $columnKey, string|int|null $indexKey = null): array
//    array_combine() - not planning to implement
//    compact() - not planning to implement
contains(mixed $needle, bool $strict = false): bool // alias for inArray()
count(): int
//    array_count_values() - not implemented
current(): mixed|false
//    array_diff() - not implemented
//    array_diff_assoc() - not implemented
//    array_diff_key() - not implemented
//    array_diff_uassoc() - not implemented
//    array_diff_ukey() - not implemented
//    each() - not planning to implement
end(): mixed|false
exchangeArray(object|array $array): array
//    extract() - not planning to implement
//    array_fill() - not planning to implement
//    array_fill_keys() - not planning to implement
filter(?callable $callback = null, int $mode = 0): array
flip(): array
getArrayCopy(): array
getFlags(): int
getIterator(): Iterator
getIteratorClass(): string
inArray(mixed $needle, bool $strict = false): bool
//    array_intersect() - not implemented
//    array_intersect_assoc() - not implemented
//    array_intersect_key() - not implemented
//    array_intersect_uassoc() - not implemented
//    array_intersect_ukey() - not implemented
isList(): bool
key(): int|string|null
keyExists(mixed $key): bool
keyFirst(): string|int|null
keyLast(): string|int|null
keys(mixed $filterValue = null, bool $strict = false): array
kRSort(int $flags = SORT_REGULAR): bool
kSort(int $flags = SORT_REGULAR): bool
//    list() - not planning to implement
map(?callable $callback): array
max(): mixed
//    array_merge() - not implemented
//    array_merge_recursive() - not implemented
min(): mixed
//    array_multisort() - not implemented
natCaseSort(): bool
natSort(): bool
next(): mixed|false
offsetExists(mixed $key): bool
offsetGet(mixed $key): mixed
offsetSet(mixed $key, mixed $value): void
offsetUnset(mixed $key): void
//    array_pad() - not implemented
pop(): mixed|null
//    pos() - not planning to implement
prev(): mixed|false
//    array_product() - not implemented
push(mixed ...$values): int
rand(int $num = 1): array|string|int
//    range() - not planning to implement
//    array_reduce() - not implemented
//    array_replace() - not implemented
//    array_replace_recursive() - not implemented
reset(): mixed|false
reverse(bool $preserveKeys = false): void
rNatCaseSort(): bool
rNatSort(): bool
rSort(int $flags = SORT_REGULAR): bool
search(mixed $needle, bool $strict = false): string|int|false
serialize(): string
setFlags(int $flags): void
setIteratorClass(string $iteratorClass): void
shift(): mixed
shuffle(): bool
//    sizeof() - not planning to implement
slice(int $offset, ?int $length = null, bool $preserveKeys = false): array
sort(int $flags = SORT_REGULAR): bool
//    array_splice() - not implemented
//    array_sum() - not implemented
uASort(callable $callback): bool
//    array_udiff() - not implemented
//    array_udiff_assoc() - not implemented
//    array_udiff_uassoc() - not implemented
//    array_uintersect() - not implemented
//    array_uintersect_assoc() - not implemented
//    array_uintersect_uassoc() - not implemented
uKSort(callable $callback): bool
unique(int $flags = SORT_STRING): array
unserialize(string $data): void
unshift(mixed ...$values): int
uSort(callable $callback): bool
values(): array
//    array_walk() - not implemented
//    array_walk_recursive() - not implemented
```



## Notes

ArrayObjects have a key `TKey` and value `TValue` type. You can define them in your classes when you extend from `ArrayObjectExtendend`.

For methods like `values()` and `map(..)`, a key consideration in having them return plain arrays is those key and value type requirements. If they updated the object, or returned new instances of the class, we can't be sure their keys and values would be valid types.



## On-Change Hook

Whenever a change is made to the data in an `ArrayObjectExtendend` instance, it calls `onAfterUpdate()`. This gives you an opportunity to clear internal caches, etc.

``` php
use ArrayAccess;
use CodeDistortion\ArrayObject\ArrayObjectExtended;
use Countable;
use IteratorAggregate;
use Serializable;

/**
 * @template TKey of integer|string
 * @template TValue of string
 * @template-implements IteratorAggregate<TKey, TValue>
 * @template-implements ArrayAccess<TKey, TValue>
 */
class MyArrayObject extends ArrayObjectExtended implements IteratorAggregate, ArrayAccess, Serializable, Countable
{
    /**
     * A hook that's called when the contents of this object has changed.
     *
     * @return void
     */
    protected function onAfterUpdate(): void
    {
        // clear internal caches, etc.
    }
}

$myArrayObject = new MyArrayObject();
// examples of methods that trigger onAfterUpdate()
$myArrayObject->unshift('hi');
$myArrayObject->push('there');
$myArrayObject->rSort();
```



## Testing

``` bash
composer test
```



## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.



### SemVer

This library uses [SemVer 2.0.0](https://semver.org/) versioning. This means that changes to `X` indicate a breaking change: `0.0.X`, `0.X.y`, `X.y.z`. When this library changes to version 1.0.0, 2.0.0 and so forth, it doesn't indicate that it's necessarily a notable release, it simply indicates that the changes were breaking.



## Treeware

This package is [Treeware](https://treeware.earth). If you use it in production, then we ask that you [**buy the world a tree**](https://plant.treeware.earth/code-distortion/array-object-extended) to thank us for our work. By contributing to the Treeware forest you’ll be creating employment for local families and restoring wildlife habitats.



## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.



### Code of Conduct

Please see [CODE_OF_CONDUCT](.github/CODE_OF_CONDUCT.md) for details.



### Security

If you discover any security related issues, please email tim@code-distortion.net instead of using the issue tracker.



## Credits

- [Tim Chandler](https://github.com/code-distortion)



## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
