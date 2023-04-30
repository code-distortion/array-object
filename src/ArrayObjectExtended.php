<?php

namespace CodeDistortion\ArrayObjectExtended;

use ArrayAccess;
use ArrayIterator;
use ArrayObject;
use Countable;
use IteratorAggregate;
use Serializable;

/**
 * Class that extends PHP's ArrayObject, adding missing non-OOP methods to it.
 *
 * It overrides existing ArrayObject methods to now also call hook method onAfterUpdate() when changes are made.
 *
 * Contains new methods based on PHP's list of regular array functions
 * - https://www.php.net/manual/en/ref.array.php
 *
 * @codingStandardsIgnoreStart
 *
 * @see https://php.net/manual/en/class.arrayobject.php
 * @see https://www.php.net/manual/en/ref.array.php
 *
 * @template TKey
 * @template TValue
 * @template-implements IteratorAggregate<TKey, TValue>
 * @template-implements ArrayAccess<TKey, TValue>
 *
 * @codingStandardsIgnoreEnd
 */
class ArrayObjectExtended extends ArrayObject implements IteratorAggregate, ArrayAccess, Serializable, Countable
{
    /** @var ArrayIterator|null An internal cache, because $this->getIterator() gives a different instance each time. */
    private ?ArrayIterator $iterator = null;



    /**
     * Construct a new array object
     *
     * @see https://php.net/manual/en/arrayobject.construct.php
     *
     * @param array<TValue>|object        $array         The input parameter accepts an array or an Object.
     *
     * @param integer                     $flags         Flags to control the behaviour of the ArrayObject object.
     * @param class-string<ArrayIterator> $iteratorClass Specify the class that will be used for iteration of the
     *                                                   ArrayObject object. ArrayIterator is the default class used.
     */
    final public function __construct(
        array|object $array = [],
        int $flags = 0,
        string $iteratorClass = ArrayIterator::class
    ) {
        parent::__construct($array, $flags, $iteratorClass);
    }



    /**
     * Get and cache the current iterator, used because $this->getIterator() gives a different instance each time.
     *
     * @return ArrayIterator
     */
    private function myIterator(): ArrayIterator
    {
        return $this->iterator ??= $this->getIterator();
    }

    /**
     * Reset the array-pointer to the beginning of the array - by resetting the cached iterator.
     *
     * @return void
     */
    private function resetArrayPointer(): void
    {
        $this->iterator = null;
    }



    // the append() method isn't needed, because the parent version ends up calling offsetSet(), which calls the
    // onAfterUpdate() hook.

//    /**
//     * Appends the value.
//     *
//     * @link https://php.net/manual/en/arrayobject.append.php
//     *
//     * @param TValue $value The value being appended.
//     * @return void
//     */
//    public function append(mixed $value): void
//    {
//        parent::append($value);
//
//        $this->onAfterUpdate();
//    }

    /**
     * Sort by value in reverse order, and maintain index association.
     *
     * @see https://www.php.net/manual/en/function.arsort.php
     *
     * @param integer $flags Optional.
     * @return boolean
     */
    public function aRSort(int $flags = SORT_REGULAR): bool
    {
        $array = parent::getArrayCopy();
        $return = arsort($array, $flags);
        $this->resetArrayPointer();
        $this->exchangeArray($array); // calls onAfterUpdate()

        return $return;
    }

    /**
     * Sort the entries by value.
     *
     * @link https://php.net/manual/en/arrayobject.asort.php
     *
     * @param integer $flags Optional.
     * @return true
     */
    public function aSort(int $flags = SORT_REGULAR): true
    {
        parent::asort($flags);

        $this->resetArrayPointer();
        $this->onAfterUpdate();

        return true;
    }

    /**
     * Changes the case of all keys in an array.
     *
     * @see https://php.net/manual/en/function.array-change-key-case.php
     *
     * @param integer $case Either CASE_UPPER or CASE_LOWER (default).
     * @return boolean
     */
    public function changeKeyCase(int $case = CASE_LOWER): bool
    {
        $array = array_change_key_case(parent::getArrayCopy(), $case);
        $this->exchangeArray($array); // calls onAfterUpdate()

        return true;
    }

    /**
     * Split an array into chunks.
     *
     * @see https://php.net/manual/en/function.array-chunk.php
     *
     * @param integer $length       The size of each chunk.
     * @param boolean $preserveKeys Optional - When set to true keys will be preserved. Default is false which will
     *                              reindex the chunk numerically.
     * @return static[] A multidimensional numerically indexed array, starting with zero, with each dimension containing
     *                  size elements.
     */
    public function chunk(int $length, bool $preserveKeys = false): array
    {
        $chunked = array_chunk(
            parent::getArrayCopy(),
            max(1, $length),
            $preserveKeys
        );

        $return = [];
        foreach ($chunked as $index => $chunk) {
            $return[$index] = new static($chunk);
        }

        return $return;
    }

    /**
     * Return the values from a single column in the input array.
     *
     * @see https://secure.php.net/manual/en/function.array-column.php
     *
     * @param string|integer|null $columnKey The column of values to return. This value may be the integer key of the
     *                                       column you wish to retrieve, or it may be the string key name for an
     *                                       associative array. It may also be NULL to return complete arrays (useful
     *                                       together with index_key to reindex the array).
     * @param string|integer|null $indexKey  Optional - The column to use as the index/keys for the returned array. This
     *                                       value may be the integer key of the column, or it may be the string key
     *                                       name.
     * @return array<string|integer, mixed>
     */
    public function column(string|int|null $columnKey, string|int|null $indexKey = null): array
    {
        return array_column(
            parent::getArrayCopy(),
            $columnKey,
            $indexKey
        );
    }

//    array_column()
//    array_combine()
//    compact()

    /**
     * Checks if a value exists in the array - alias for inArray().
     *
     * @see https://php.net/manual/en/function.in-array.php
     *
     * @param mixed   $needle The searched value. If needle is a string, the comparison is done in a case-sensitive
     *                        manner.
     * @param boolean $strict Optional - If the third parameter strict is set to true then the in_array function will
     *                        also check the types of the needle in the haystack.
     * @return boolean True if needle is found in the array, false otherwise.
     */
    public function contains(mixed $needle, bool $strict = false): bool
    {
        return $this->inArray($needle, $strict);
    }

//    count() // already implemented by Countable
//    array_count_values()

    /**
     * Return the current element in an array.
     *
     * @see https://php.net/manual/en/function.current.php
     *
     * @return mixed|false The current function simply returns the value of the array element that's currently being
     *                     pointed to by the internal pointer. It does not move the pointer in any way. If the internal
     *                     pointer points beyond the end of the elements list or the array is empty, current returns
     *                     false.
     */
    public function current(): mixed
    {
        return $this->myIterator()->current() ?? false;
    }

//    array_diff()
//    array_diff_assoc()
//    array_diff_key()
//    array_diff_uassoc()
//    array_diff_ukey()
//    each()
//    end()

    /**
     * Set the internal pointer of an array to its last element
     *
     * @see https://php.net/manual/en/function.end.php
     *
     * @return mixed|false the value of the last element or false for empty array.
     * @meta
     */
    public function end(): mixed
    {
        $lastIndex = $this->getIterator()->count() - 1;
        $this->myIterator()->seek($lastIndex);
        return $this->current();
    }

    /**
     * Exchange the array for another one.
     *
     * @link https://php.net/manual/en/arrayobject.exchangearray.php
     *
     * @param array<TKey, TValue>|object $array The new array or object to exchange with the current array.
     * @return array<TKey, TValue> The old array.
     */
    public function exchangeArray(object|array $array): array
    {
        $return = parent::exchangeArray($array);

        $this->onAfterUpdate();

        return $return;
    }

//    extract()
//    array_fill()
//    array_fill_keys()

    /**
     * Iterates over each value, passing them to the callback function.
     *
     * If the callback function returns true, the current value from array is returned into the result array. Array keys
     * are preserved.
     *
     * @see https://php.net/manual/en/function.array-filter.php
     *
     * @param callable|null $callback Optional - The callback function to use If no callback is supplied, all entries of
     *                                input equal to false (see converting to boolean) will be removed.
     * @param integer       $mode     Optional - Flag determining what arguments are sent to callback:
     *                                ARRAY_FILTER_USE_KEY - pass key as the only argument to callback instead of the
     *                                value.
     *                                ARRAY_FILTER_USE_BOTH - pass both value and key as arguments to callback instead
     *                                of the value.
     * @return mixed[] The filtered array.
     */
    // @codingStandardsIgnoreLine @infection-ignore-all - DecrementInteger - when changing $mode to -1, it acts the same as $mode = 0 anyway
    public function filter(?callable $callback = null, int $mode = 0): array
    {
        return is_callable($callback)
            ? array_filter(parent::getArrayCopy(), $callback, $mode)
            : array_filter(parent::getArrayCopy());
    }

    /**
     * Exchanges all keys with their associated values in an array.
     *
     * @see https://php.net/manual/en/function.array-flip.php
     *
     * @return array Returns the flipped array.
     */
    public function flip(): array
    {
        return array_flip(parent::getArrayCopy());
    }

    /**
     * Checks if a value exists in the array.
     *
     * @see https://php.net/manual/en/function.in-array.php
     *
     * @param mixed   $needle The searched value. If needle is a string, the comparison is done in a case-sensitive
     *                        manner.
     * @param boolean $strict Optional - If the third parameter strict is set to true then the in_array function will
     *                        also check the types of the needle in the haystack.
     * @return boolean True if needle is found in the array, false otherwise.
     */
    public function inArray(mixed $needle, bool $strict = false): bool
    {
        return in_array($needle, parent::getArrayCopy(), $strict);
    }

//    array_intersect()
//    array_intersect_assoc()
//    array_intersect_key()
//    array_intersect_uassoc()
//    array_intersect_ukey()

    /**
     * Checks whether the current array is a list.
     *
     * @link https://secure.php.net/array_is_list
     *
     * @return boolean Return true if the array keys are 0 .. count($array) - 1 in that order. For other arrays, it
     *                 returns false. For non-arrays, it throws a TypeError.
     */
    public function isList(): bool
    {
        $array = parent::getArrayCopy();
        return array_is_list($array);

//        if (function_exists('array_is_list')) {
//            return array_is_list($array);
//        }
//
//        // polyfill for older versions of PHP
//        if (!count($array)) {
//            return true;
//        }
//
//        $keys = array_keys($array);
//        $range = range(0, count($array) - 1);
//
//        return $keys === $range;
    }

    /**
     * Fetch a key from an array
     *
     * @see https://php.net/manual/en/function.key.php
     *
     * @return integer|string|null The key function simply returns the key of the array element that's currently being
     *                             pointed to by the internal pointer. It does not move the pointer in any way. If the
     *                             internal pointer points beyond the end of the elements list or the array is empty,
     *                             key returns null.
     */
    public function key(): mixed
    {
        return $this->myIterator()->key();
    }

    /**
     * Returns whether the requested index exists.
     *
     * Alias for offsetExists().
     *
     * @see https://php.net/manual/en/arrayobject.offsetexists.php
     *
     * @param TKey $key The index being checked.
     * @return boolean True if the requested index exists, otherwise false.
     */
    public function keyExists(mixed $key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * Gets the first key.
     *
     * Get the first key without affecting the internal array pointer.
     *
     * @see https://secure.php.net/array_key_first
     *
     * @return string|integer|null Returns the first key of array if the array is not empty; NULL otherwise.
     */
    public function keyFirst(): string|int|null
    {
        return array_key_first(parent::getArrayCopy());
    }

    /**
     * Gets the last key.
     *
     * Get the last key without affecting the internal array pointer.
     *
     * @see https://secure.php.net/array_key_last
     *
     * @return string|integer|null Returns the last key of array if the array is not empty; NULL otherwise.
     */
    public function keyLast(): string|int|null
    {
        return array_key_last(parent::getArrayCopy());
    }

    /**
     * Return all the keys or a subset of the keys.
     *
     * @see https://php.net/manual/en/function.array-keys.php
     *
     * @param mixed   $filterValue Optional - If specified, then only keys containing these values are returned.
     * @param boolean $strict      Optional - Determines if strict comparison (===) should be used during the search.
     * @return array<integer, integer|string>
     */
    public function keys(mixed $filterValue = null, bool $strict = false): array
    {
        return func_num_args() > 0
            ? array_keys(parent::getArrayCopy(), $filterValue, $strict)
            : array_keys(parent::getArrayCopy());
    }

    /**
     * Sort by key in reverse order.
     *
     * @see https://www.php.net/manual/en/function.ksort.php
     *
     * @param integer $flags Optional.
     * @return boolean
     */
    public function kRSort(int $flags = SORT_REGULAR): bool
    {
        $array = parent::exchangeArray([]);
        $return = krsort($array, $flags);
        $this->resetArrayPointer();
        $this->exchangeArray($array); // calls onAfterUpdate()

        return $return;
    }

    /**
     * Sort the entries by key.
     *
     * @link https://php.net/manual/en/arrayobject.ksort.php
     *
     * @param integer $flags Optional.
     * @return true
     */
    public function kSort(int $flags = SORT_REGULAR): true
    {
        parent::ksort($flags);

        $this->resetArrayPointer();
        $this->onAfterUpdate();

        return true;
    }

//    list()

    /**
     * Applies the callback to the elements of the given arrays
     *
     * @see https://php.net/manual/en/function.array-map.php
     * @param callable|null $callback Callback function to run for each element in each array.
     * @return array<integer|string,mixed> An array containing all the elements after applying the callback function to
     *                                     each one.
     * @meta
     */
    public function map(?callable $callback): array
    {
        return array_map($callback, parent::getArrayCopy());
    }

    /**
     * Find the highest value.
     *
     * @see https://php.net/manual/en/function.max.php
     *
     * @return mixed Max returns the numerically highest of the array's values.
     */
    public function max(): mixed
    {
        return max(parent::getArrayCopy());
    }

//    array_merge()
//    array_merge_recursive()

    /**
     * Find the lowest value.
     *
     * @see https://php.net/manual/en/function.min.php
     *
     * @return mixed Min returns the numerically lowest of the array's values.
     */
    public function min(): mixed
    {
        return min(parent::getArrayCopy());
    }

//    array_multisort()

    /**
     * Sort an array using a case-insensitive "natural order" algorithm, and maintain key association.
     *
     * @link https://php.net/manual/en/arrayobject.natcasesort.php
     *
     * @return true
     */
    public function natCaseSort(): true
    {
        parent::natcasesort();

        $this->resetArrayPointer();
        $this->onAfterUpdate();

        return true;
    }

    /**
     * Sort entries using a "natural order" algorithm, and maintain key association.
     *
     * @link https://php.net/manual/en/arrayobject.natsort.php
     *
     * @return true
     */
    public function natSort(): true
    {
        parent::natsort();

        $this->resetArrayPointer();
        $this->onAfterUpdate();

        return true;
    }

    /**
     * Advance the internal array pointer of an array.
     *
     * @see https://php.net/manual/en/function.next.php
     *
     * @return mixed|false The array value in the next place that's pointed to by the internal array pointer, or false
     *                     if there are no more elements.
     */
    public function next(): mixed
    {
        $this->myIterator()->next();
        return $this->current();
    }

    /**
     * Sets the value at the specified index to newval.
     *
     * @link https://php.net/manual/en/arrayobject.offsetset.php
     *
     * @param TKey   $key   The index being set.
     * @param TValue $value The new value for the index.
     * @return void
     */
    public function offsetSet(mixed $key, mixed $value): void
    {
        parent::offsetSet($key, $value);

        $this->onAfterUpdate();
    }

    /**
     * Unsets the value at the specified index.
     *
     * @link https://php.net/manual/en/arrayobject.offsetunset.php
     *
     * @param TKey $key The index being unset.
     * @return void
     */
    public function offsetUnset(mixed $key): void
    {
        parent::offsetUnset($key);

        $this->onAfterUpdate();
    }

//    array_pad()

    /**
     * Pop the element off the end.
     *
     * @link https://php.net/manual/en/function.array-pop.php
     *
     * @return mixed|null The last value of array. If array is empty (or is not an array), null will be returned.
     */
    public function pop(): mixed
    {
        $array = parent::getArrayCopy();
        $return = array_pop($array);
        $this->resetArrayPointer();
        $this->exchangeArray($array); // calls onAfterUpdate()

        return $return;
    }

//    pos()

    /**
     * Rewind the internal array pointer.
     *
     * @see https://php.net/manual/en/function.prev.php
     *
     * @return mixed|false the array value in the previous place that's pointed to by the internal array pointer, or
     *                     false if there are no more elements.
     */
    public function prev(): mixed
    {
        $keys = array_keys(parent::getArrayCopy());
        $currentKey = $this->key();

        // if already at the start of the array…
        $pos = array_search($currentKey, $keys, true);
        if ($pos == 0) {
            // force the array pointer to be out of bounds
            $this->end();
            $this->next();
            return $this->current();
        }

        // jump to the previous position
        $this->myIterator()->seek($pos - 1);
        return $this->current();
    }

//    array_product()

    /**
     * Push elements onto the end.
     *
     * @see https://php.net/manual/en/function.array-push.php
     *
     * @param mixed ...$values The pushed variables.
     * @return integer The number of elements in the array.
     */
    public function push(mixed ...$values): int
    {
        $array = parent::getArrayCopy();
        $return = array_push($array, ...$values);
        $this->exchangeArray($array); // calls onAfterUpdate()

        return $return;
    }

    /**
     * Pick one or more random keys out of an array.
     *
     * @see https://php.net/manual/en/function.array-rand.php
     *
     * @param integer $num Optional - Specifies how many entries you want to pick.
     * @return integer|string|mixed[] If you are picking only one entry, array_rand returns the key for a random entry.
     *                                Otherwise, it returns an array of keys for the random entries. This is done so
     *                                that you can pick random keys as well as values out of the array.
     */
    public function rand(int $num = 1): array|string|int
    {
        return array_rand(parent::getArrayCopy(), $num);
    }

//    range()
//    array_reduce()
//    array_replace()
//    array_replace_recursive()

    /**
     * Set the internal pointer of an array to its first element.
     *
     * @see https://php.net/manual/en/function.reset.php
     *
     * @return mixed|false the value of the first array element, or false if the array is empty.
     */
    public function reset(): mixed
    {
        $this->myIterator()->rewind();
        return $this->current();
    }

    /**
     * Reverse the items.
     *
     * @param boolean $preserveKeys Optional - If set to true, keys are preserved.
     * @return void
     */
    public function reverse(bool $preserveKeys = false): void
    {
        $array = array_reverse(parent::getArrayCopy(), $preserveKeys);
        $this->exchangeArray($array); // calls onAfterUpdate()
    }

    /**
     * Sort using a case-insensitive "natural order" algorithm, and maintain index association.
     *
     * @see https://php.net/manual/en/arrayobject.natcasesort.php
     *
     * @return boolean
     */
    public function rNatCaseSort(): bool
    {
        parent::natcasesort();

        $array = array_reverse(parent::getArrayCopy(), true);
        $this->exchangeArray($array); // calls onAfterUpdate()

        return true;
    }

    /**
     * Sort using a "natural order" algorithm, and maintain index association.
     *
     * @see https://php.net/manual/en/arrayobject.natsort.php
     *
     * @return boolean
     */
    public function rNatSort(): bool
    {
        parent::natsort();

        $array = array_reverse(parent::getArrayCopy(), true);
        $this->exchangeArray($array); // calls onAfterUpdate()

        return true;
    }

    /**
     * Sort by value in reverse order.
     *
     * @param integer $flags Optional.
     * @return boolean
     */
    public function rSort(int $flags = SORT_REGULAR): bool
    {
        $array = parent::getArrayCopy();
        $return = rsort($array, $flags);
        $this->resetArrayPointer();
        $this->exchangeArray($array); // calls onAfterUpdate()

        return $return;
    }

    /**
     * Searches the array for a given value and returns the first corresponding key if successful.
     *
     * @link https://php.net/manual/en/function.array-search.php
     *
     * @param mixed   $needle The searched value. If needle is a string, the comparison is done in a case-sensitive
     *                        manner.
     * @param boolean $strict Optional - If the second parameter strict is set to true then the search function will
     *                        also check the needle's type.
     * @return integer|string|false The key for needle if it is found in the array, false otherwise. If needle is found
     *                              in haystack more than once, the first matching key is returned. To return the keys
     *                              for all matching values, use keys() with the optional search_value parameter
     *                              instead.
     */
    public function search(mixed $needle, bool $strict = false): string|int|false
    {
        return array_search($needle, parent::getArrayCopy(), $strict);
    }

    /**
     * Shift an element off the beginning.
     *
     * @see https://php.net/manual/en/function.array-shift.php
     *
     * @return mixed|null the shifted value, or null if array is empty or is not an array.
     */
    public function shift(): mixed
    {
        $array = parent::getArrayCopy();
        $return = array_shift($array);
        $this->resetArrayPointer();
        $this->exchangeArray($array); // calls onAfterUpdate()

        return $return;
    }

    /**
     * Shuffle an array.
     *
     * @see https://php.net/manual/en/function.shuffle.php
     *
     * @return boolean true on success or false on failure.
     */
    public function shuffle(): bool
    {
        $array = parent::getArrayCopy();
        $return = shuffle($array);
        $this->resetArrayPointer();
        $this->exchangeArray($array); // calls onAfterUpdate()

        return $return;
    }

//    sizeof()

    /**
     * Extract a slice.
     *
     * @see https://php.net/manual/en/function.array-slice.php
     *
     * @param integer      $offset       If offset is non-negative, the sequence will start at that offset in the array.
     *                                   If offset is negative, the sequence will start that far from the end of the
     *                                   array.
     * @param integer|null $length       Optional - If length is given and is positive, then the sequence will have that
     *                                   many elements in it. If length is given and is negative then the sequence will
     *                                   stop that many elements from the end of the array. If it is omitted, then the
     *                                   sequence will have everything from offset up until the end of the array.
     * @param boolean      $preserveKeys Optional - Note that slice() will reorder and reset the array indices by
     *                                   default. You can change this behaviour by setting $preserveKeys to true.
     * @return mixed[] The slice.
     */
    public function slice(int $offset, ?int $length = null, bool $preserveKeys = false): array
    {
        return array_slice(parent::getArrayCopy(), $offset, $length, $preserveKeys);
    }

    /**
     * Sort by value.
     *
     * @param integer $flags Optional.
     * @return boolean
     */
    public function sort(int $flags = SORT_REGULAR): bool
    {
        $array = parent::getArrayCopy();
        $return = sort($array, $flags);
        $this->resetArrayPointer();
        $this->exchangeArray($array); // calls onAfterUpdate()

        return $return;
    }

//    array_splice()
//    array_sum()

    /**
     * Sort the entries with a user-defined comparison function, and maintain key association.
     *
     * @link https://php.net/manual/en/arrayobject.uasort.php
     *
     * @param callable(TValue, TValue):integer $callback Function cmp_function should accept two
     *                                                   parameters which will be filled by pairs of entries.
     *                                                   The comparison function must return an integer less than, equal
     *                                                   to, or greater than zero if the first argument is considered to
     *                                                   be respectively less than, equal to, or greater than the
     *                                                   second.
     * @return true
     */
    public function uASort(callable $callback): true
    {
        parent::uasort($callback);

        $this->resetArrayPointer();
        $this->onAfterUpdate();

        return true;
    }

//    array_udiff()
//    array_udiff_assoc()
//    array_udiff_uassoc()
//    array_uintersect()
//    array_uintersect_assoc()
//    array_uintersect_uassoc()

    /**
     * Sort the entries by keys using a user-defined comparison function.
     *
     * @link https://php.net/manual/en/arrayobject.uksort.php
     *
     * @param callable(TValue, TValue):integer $callback The callback comparison function.
     *
     *                                                   Function cmp_function should accept two
     *                                                   parameters which will be filled by pairs of entry keys.
     *                                                   The comparison function must return an integer less than, equal
     *                                                   to, or greater than zero if the first argument is considered to
     *                                                   be respectively less than, equal to, or greater than the
     *                                                   second.
     * @return true
     */
    public function uKSort(callable $callback): true
    {
        parent::uksort($callback);

        $this->resetArrayPointer();
        $this->onAfterUpdate();

        return true;
    }

    /**
     * Removes duplicate values from an array.
     *
     * @see https://php.net/manual/en/function.array-unique.php
     *
     * @param integer $flags Optional - The optional second parameter sort_flags may be used to modify the sorting
     *                       behavior using these values:
     *                       Sorting type flags:
     *                       SORT_REGULAR - compare items normally (don't change types)
     *                       SORT_NUMERIC - compare items numerically
     *                       SORT_STRING - compare items as strings
     *                       SORT_LOCALE_STRING - compare items as strings, based on the current locale.
     * @return mixed[] The filtered array.
     */
    public function unique(int $flags = SORT_STRING): array
    {
        return array_unique(parent::getArrayCopy(), $flags);
    }

    /**
     * Unserialize an ArrayObject.
     *
     * @link https://php.net/manual/en/arrayobject.unserialize.php
     *
     * @param string $data The serialized ArrayObject.
     * @return void
     */
    public function unserialize(string $data): void
    {
        parent::unserialize($data);

        $this->onAfterUpdate();
    }

    /**
     * Prepend elements to the beginning of an array.
     *
     * @see https://php.net/manual/en/function.array-unshift.php
     *
     * @param mixed ...$values The prepended variables.
     * @return integer The number of elements in the array.
     */
    public function unshift(mixed ...$values): int
    {
        $array = parent::getArrayCopy();
        $return = array_unshift($array, ...$values);
        $this->resetArrayPointer();
        $this->exchangeArray($array); // calls onAfterUpdate()

        return $return;
    }

    /**
     * Sort an array by values using a user-defined comparison function.
     *
     * @see https://php.net/manual/en/function.usort.php
     *
     * @param callable $callback The comparison function must return an integer less than, equal to, or greater than
     *                           zero if the first argument is considered to be respectively less than, equal to, or
     *                           greater than the second.
     * @return boolean True on success or false on failure.
     */
    public function uSort(callable $callback): bool
    {
        $array = parent::getArrayCopy();
        $return = usort($array, $callback);
        $this->resetArrayPointer();
        $this->exchangeArray($array); // calls onAfterUpdate()

        return $return;
    }

    /**
     * Return all the values.
     *
     * @link https://php.net/manual/en/function.array-values.php
     *
     * @return array<integer,mixed>
     */
    public function values(): array
    {
        return array_values(parent::getArrayCopy());
    }

//    array_walk()
//    array_walk_recursive()





    /**
     * A hook that's called when the contents of this object has changed.
     *
     * Allow for child classes to override (to clear internal caches etc.).
     *
     * @return void
     */
    protected function onAfterUpdate(): void
    {
    }
}
