<?php

namespace CodeDistortion\ArrayObjectExtended\Tests\Unit;

use CodeDistortion\ArrayObjectExtended\Tests\PHPUnitTestCase;
use CodeDistortion\ArrayObjectExtended\Tests\Support\TestArrayObjectExtended;
use Exception;
use Throwable;
use ValueError;

/**
 * Test the ArrayObjectExtended class.
 *
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
class ArrayObjectExtendedTest extends PHPUnitTestCase
{
    /**
     * Test the append(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_append_method(): void
    {
        $a = new TestArrayObjectExtended([]);
        self::assertSame(0, count($a));

        $a[] = 'one';
        self::assertSame([0 => 'one'], $a->getArrayCopy());
        self::assertSame(1, $a->getChangedCount());

        // apply again
        $a[] = 'two';
        self::assertSame([0 => 'one', 1 => 'two'], $a->getArrayCopy());
        self::assertSame(2, $a->getChangedCount());

        // perform the same, but call append(..) explicitly
        $a = new TestArrayObjectExtended([]);
        self::assertSame(0, count($a));

        $a->append('one');
        self::assertSame([0 => 'one'], $a->getArrayCopy());
        self::assertSame(1, $a->getChangedCount());

        // apply again
        $a->append('two');
        self::assertSame([0 => 'one', 1 => 'two'], $a->getArrayCopy());
        self::assertSame(2, $a->getChangedCount());
    }

    /**
     * Test the aRSort(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_arsort_method(): void
    {
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven', 12 => 'twelve']);
        self::assertTrue($a->aRSort());
        self::assertSame([12 => 'twelve', 10 => 'ten', 11 => 'eleven'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // apply again
        self::assertTrue($a->aRSort());
        self::assertSame([12 => 'twelve', 10 => 'ten', 11 => 'eleven'], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());

        // check that sorting flag is passed through
        $a = new TestArrayObjectExtended([1 => 1, 2 => 2, 11 => 11, '12' => '12']);
        self::assertTrue($a->aRSort(SORT_STRING));
        self::assertSame([2 => 2, '12' => '12', 11 => 11, 1 => 1], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        self::assertTrue($a->aRSort(SORT_NUMERIC));
        self::assertSame(['12' => '12', 11 => 11, 2 => 2, 1 => 1], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());
    }

    /**
     * Test the aSort(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_asort_method(): void
    {
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven', 12 => 'twelve']);
        self::assertTrue($a->aSort());
        self::assertSame([11 => 'eleven', 10 => 'ten', 12 => 'twelve'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // apply again
        self::assertTrue($a->aSort());
        self::assertSame([11 => 'eleven', 10 => 'ten', 12 => 'twelve'], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());

        // check that sorting flag is passed through
        $a = new TestArrayObjectExtended([1 => 1, 2 => 2, 11 => 11, '12' => '12']);
        self::assertTrue($a->aSort(SORT_STRING));
        self::assertSame([1 => 1, 11 => 11, '12' => '12', 2 => 2], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        self::assertTrue($a->aSort(SORT_NUMERIC));
        self::assertSame([1 => 1, 2 => 2, 11 => 11, '12' => '12'], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());
    }

    /**
     * Test the changeKeyCase(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_change_key_case_method(): void
    {
        $start = ['oNe' => 1, 'TwO' => 2];
        $a = new TestArrayObjectExtended($start);

        self::assertTrue($a->changeKeyCase());
        self::assertSame(['one' => 1, 'two' => 2], $a->getArrayCopy());
        self::assertSame(1, $a->getChangedCount());

        self::assertTrue($a->changeKeyCase(CASE_UPPER)); // upper case
        self::assertSame(['ONE' => 1, 'TWO' => 2], $a->getArrayCopy());
        self::assertSame(2, $a->getChangedCount());

        self::assertTrue($a->changeKeyCase(CASE_LOWER)); // explicitly choose lower case
        self::assertSame(['one' => 1, 'two' => 2], $a->getArrayCopy());
        self::assertSame(3, $a->getChangedCount());
    }

    /**
     * Test the chunk(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_chunk_method(): void
    {
        $start = [50 => 100, 51 => 101, 52 => 102, 53 => 103];
        $a = new TestArrayObjectExtended($start);

        $chunks = $a->chunk(0);
        self::assertSame(4, count($chunks));
        self::assertInstanceOf(TestArrayObjectExtended::class, $chunks[0]);
        self::assertSame([0 => 100], $chunks[0]->getArrayCopy());
        self::assertInstanceOf(TestArrayObjectExtended::class, $chunks[1]);
        self::assertSame([0 => 101], $chunks[1]->getArrayCopy());
        self::assertInstanceOf(TestArrayObjectExtended::class, $chunks[2]);
        self::assertSame([0 => 102], $chunks[2]->getArrayCopy());
        self::assertInstanceOf(TestArrayObjectExtended::class, $chunks[3]);
        self::assertSame([0 => 103], $chunks[3]->getArrayCopy());

        $chunks = $a->chunk(2);
        self::assertSame(2, count($chunks));
        self::assertInstanceOf(TestArrayObjectExtended::class, $chunks[0]);
        self::assertSame([0 => 100, 1 => 101], $chunks[0]->getArrayCopy());
        self::assertInstanceOf(TestArrayObjectExtended::class, $chunks[1]);
        self::assertSame([0 => 102, 1 => 103], $chunks[1]->getArrayCopy());

        $chunks = $a->chunk(2, false); // explicitly don't preserve keys
        self::assertSame(2, count($chunks));
        self::assertInstanceOf(TestArrayObjectExtended::class, $chunks[0]);
        self::assertSame([0 => 100, 1 => 101], $chunks[0]->getArrayCopy());
        self::assertInstanceOf(TestArrayObjectExtended::class, $chunks[1]);
        self::assertSame([0 => 102, 1 => 103], $chunks[1]->getArrayCopy());

        $chunks = $a->chunk(2, true); // preserve keys
        self::assertSame(2, count($chunks));
        self::assertInstanceOf(TestArrayObjectExtended::class, $chunks[0]);
        self::assertSame([50 => 100, 51 => 101], $chunks[0]->getArrayCopy());
        self::assertInstanceOf(TestArrayObjectExtended::class, $chunks[1]);
        self::assertSame([52 => 102, 53 => 103], $chunks[1]->getArrayCopy());

        self::assertSame(0, $a->getChangedCount());

        // check there's no change to the array
        $a = new TestArrayObjectExtended($start);
        $a->chunk(2);
        self::assertSame($start, $a->getArrayCopy());
    }

    /**
     * Test the chunk(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_column_method(): void
    {
        $data = [
            [
                'id' => 1,
                'name' => 'bob',
                5 => 'BOB',
            ],
            [
                'id' => 2,
                'name' => 'jim',
                5 => 'JIM',
            ],
        ];
        $array = new TestArrayObjectExtended($data);



        // $columnKey: integer, $indexKey: null
        $column = $array->column(5);
        $expected = [
            0 => 'BOB',
            1 => 'JIM',
        ];
        self::assertSame($expected, (array) $column);

        // $columnKey: integer, $indexKey: integer
        $column = $array->column(5, 5);
        $expected = [
            'BOB' => 'BOB',
            'JIM' => 'JIM',
        ];
        self::assertSame($expected, (array) $column);

        // $columnKey: integer, $indexKey: string
        $column = $array->column(5, 'id');
        $expected = [
            1 => 'BOB',
            2 => 'JIM',
        ];
        self::assertSame($expected, (array) $column);



        // $columnKey: string, $indexKey: null
        $column = $array->column('name');
        $expected = [
            0 => 'bob',
            1 => 'jim',
        ];
        self::assertSame($expected, $column);

        // $columnKey: string, $indexKey: integer
        $column = $array->column('name', 5);
        $expected = [
            'BOB' => 'bob',
            'JIM' => 'jim',
        ];
        self::assertSame($expected, $column);

        // $columnKey: string, $indexKey: string
        $column = $array->column('name', 'id');
        $expected = [
            1 => 'bob',
            2 => 'jim',
        ];
        self::assertSame($expected, $column);



        // $columnKey: null, $indexKey: null
        $column = $array->column(null);
        $expected = (array) $array;
        self::assertSame($expected, $column);

        // $columnKey: null, $indexKey: integer
        $column = $array->column(null, 5);
        $expected = [
            'BOB' => [
                'id' => 1,
                'name' => 'bob',
                5 => 'BOB',
            ],
            'JIM' => [
                'id' => 2,
                'name' => 'jim',
                5 => 'JIM',
            ],
        ];
        self::assertSame($expected, $column);

        // $columnKey: null, $indexKey: string
        $column = $array->column(null, 'name');
        $expected = [
            'bob' => [
                'id' => 1,
                'name' => 'bob',
                5 => 'BOB',
            ],
            'jim' => [
                'id' => 2,
                'name' => 'jim',
                5 => 'JIM',
            ],
        ];
        self::assertSame($expected, $column);

        self::assertSame(0, $array->getChangedCount());
    }

    /**
     * Test the contains(..) method - alias for inArray().
     *
     * @test
     *
     * @return void
     */
    public static function test_contains_method(): void
    {
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven', 12 => 'twelve', 'false' => false]);

        self::assertTrue($a->contains('ten'));
        self::assertTrue($a->contains(false));
        self::assertTrue($a->contains(true));
        self::assertTrue($a->contains(0));
        self::assertFalse($a->contains(1));

        self::assertTrue($a->contains('ten', false));
        self::assertTrue($a->contains(false, false));
        self::assertTrue($a->contains(true, false));
        self::assertTrue($a->contains(0, false));
        self::assertFalse($a->contains(1, false));

        self::assertTrue($a->contains('ten', true));
        self::assertTrue($a->contains(false, true));
        self::assertFalse($a->contains(true, true));
        self::assertFalse($a->contains(0, true));
        self::assertFalse($a->contains(1, true));

        self::assertSame(0, $a->getChangedCount());
    }

    /**
     * Test the exchangeArray(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_exchange_array_method(): void
    {
        $origArray = [10 => 'ten', 11 => 'eleven'];
        $a = new TestArrayObjectExtended($origArray);
        $result = $a->exchangeArray([20 => 'twenty', 21 => 'twenty one']);

        self::assertSame([20 => 'twenty', 21 => 'twenty one'], $a->getArrayCopy());
        self::assertSame($origArray, $result);
        self::assertSame(1, $a->getChangedCount());
    }

    /**
     * Test the filter(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_filter_method(): void
    {
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven']);
        self::assertSame([10 => 'ten'], $a->filter(fn($value) => $value === 'ten'));

        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven']);
        self::assertSame([10 => 'ten'], $a->filter(fn($value) => $value === 'ten', 0));

        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven']);
        self::assertSame([10 => 'ten'], $a->filter(fn($key) => $key === 10, ARRAY_FILTER_USE_KEY));

        $a = new TestArrayObjectExtended([10 => 't', 11 => 'eleven']);
        self::assertSame([10 => 't'], $a->filter(fn($value, $key) => "$key$value" === '10t', ARRAY_FILTER_USE_BOTH));

        self::assertSame([10 => 't', 11 => 'eleven'], $a->getArrayCopy()); // didn't change

        self::assertSame(0, $a->getChangedCount());
    }

    /**
     * Test the flip(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_flip_method(): void
    {
        $a = new TestArrayObjectExtended([]);
        self::assertIsArray($a->flip());
        self::assertSame([], $a->flip());

        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven']);
        self::assertSame(['ten' => 10, 'eleven' => 11], $a->flip());

        self::assertSame(0, $a->getChangedCount());
    }

    /**
     * Test the inArray(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_in_array_method(): void
    {
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven', 12 => 'twelve', 'false' => false]);

        self::assertTrue($a->inArray('ten'));
        self::assertTrue($a->inArray(false));
        self::assertTrue($a->inArray(true));
        self::assertTrue($a->inArray(0));
        self::assertFalse($a->inArray(1));

        self::assertTrue($a->inArray('ten', false));
        self::assertTrue($a->inArray(false, false));
        self::assertTrue($a->inArray(true, false));
        self::assertTrue($a->inArray(0, false));
        self::assertFalse($a->inArray(1, false));

        self::assertTrue($a->inArray('ten', true));
        self::assertTrue($a->inArray(false, true));
        self::assertFalse($a->inArray(true, true));
        self::assertFalse($a->inArray(0, true));
        self::assertFalse($a->inArray(1, true));

        self::assertSame(0, $a->getChangedCount());
    }

    /**
     * Test the isList(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_is_list_method(): void
    {
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven']);
        self::assertFalse($a->isList());

        self::assertSame(0, $a->getChangedCount());

        $a = new TestArrayObjectExtended([0 => 'zero', 1 => 'one']);
        self::assertTrue($a->isList());

        $a = new TestArrayObjectExtended([1 => 'one', 0 => 'zero']);
        self::assertFalse($a->isList());

        // string keys
        $a = new TestArrayObjectExtended(['0' => 'zero', '1' => 'one']);
        self::assertTrue($a->isList());

        // re-order it so it is a list
        $a->kSort();
        self::assertTrue($a->isList());

        // empty array
        $a = new TestArrayObjectExtended([]);
        self::assertTrue($a->isList());
    }

    /**
     * Test the keyExists(..) method (alias for offsetExists()).
     *
     * @test
     *
     * @return void
     */
    public static function test_key_exists_method(): void
    {
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven']);

        self::assertTrue($a->keyExists(10));
        self::assertFalse($a->keyExists(20));

        self::assertSame(0, $a->getChangedCount());
    }

    /**
     * Test the keyFirst(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_key_first_method(): void
    {
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven']);
        self::assertSame(10, $a->keyFirst());

        self::assertSame(0, $a->getChangedCount());

        // empty array
        $a = new TestArrayObjectExtended([]);
        self::assertNull($a->keyFirst());
    }

    /**
     * Test the keyLast(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_key_last_method(): void
    {
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven']);
        self::assertSame(11, $a->keyLast());

        self::assertSame(0, $a->getChangedCount());

        // empty array
        $a = new TestArrayObjectExtended([]);
        self::assertNull($a->keyLast());
    }

    /**
     * Test the keys(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_keys_method(): void
    {
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven', 'false' => 0]);

        self::assertSame([10, 11, 'false'], $a->keys());
        self::assertSame([10], $a->keys('ten'));
        self::assertSame(['false'], $a->keys(false));
        self::assertSame([], $a->keys(false, true));

        self::assertSame(0, $a->getChangedCount());
    }

    /**
     * Test the kRSort(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_krsort_method(): void
    {
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven', 12 => 'twelve']);
        self::assertTrue($a->kRSort());
        self::assertSame([12 => 'twelve', 11 => 'eleven', 10 => 'ten'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // apply again
        self::assertTrue($a->kRSort());
        self::assertSame([12 => 'twelve', 11 => 'eleven', 10 => 'ten'], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());

        // check that sorting flag is passed through
        $a = new TestArrayObjectExtended([1 => 1, 2 => 2, 11 => 11, '12' => '12']);
        self::assertTrue($a->kRSort(SORT_STRING));
        self::assertSame([2 => 2, '12' => '12', 11 => 11, 1 => 1], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        self::assertTrue($a->kRSort(SORT_NUMERIC));
        self::assertSame(['12' => '12', 11 => 11, 2 => 2, 1 => 1], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());
    }

    /**
     * Test the kSort(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_ksort_method(): void
    {
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven', 12 => 'twelve']);
        self::assertTrue($a->kSort());
        self::assertSame([10 => 'ten', 11 => 'eleven', 12 => 'twelve'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // apply again
        self::assertTrue($a->kSort());
        self::assertSame([10 => 'ten', 11 => 'eleven', 12 => 'twelve'], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());

        // check that sorting flag is passed through
        $a = new TestArrayObjectExtended([1 => 1, 2 => 2, 11 => 11, '12' => '12']);
        self::assertTrue($a->kSort(SORT_STRING));
        self::assertSame([1 => 1, 11 => 11, '12' => '12', 2 => 2], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        self::assertTrue($a->kSort(SORT_NUMERIC));
        self::assertSame([1 => 1, 2 => 2, 11 => 11, '12' => '12'], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());
    }

    /**
     * Test the map(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_map_method(): void
    {
        $a = new TestArrayObjectExtended([]);
        self::assertSame([], $a->map('strtoupper'));

        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven']);
        self::assertSame([10 => 'TEN', 11 => 'ELEVEN'], $a->map('strtoupper'));

        $callback = fn($value) => strtoupper($value);
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven']);
        self::assertSame([10 => 'TEN', 11 => 'ELEVEN'], $a->map($callback));

        self::assertSame(0, $a->getChangedCount());
    }

    /**
     * Test the max(..) method.
     *
     * @return void
     */
    public function test_max_method(): void
    {
        $caughtException = false;
        try {
            $a = new TestArrayObjectExtended([]);
            self::assertSame(0, $a->max());
        } catch (ValueError) {
            $caughtException = true;
        }
        self::assertTrue($caughtException);

        $a = new TestArrayObjectExtended([10]);
        self::assertSame(10, $a->max());

        $a = new TestArrayObjectExtended([10, 5, 20]);
        self::assertSame(20, $a->max());
        self::assertSame(0, $a->getChangedCount());
    }

    /**
     * Test the min(..) method.
     *
     * @return void
     */
    public function test_min_method(): void
    {
        $caughtException = false;
        try {
            $a = new TestArrayObjectExtended([]);
            self::assertSame(0, $a->min());
        } catch (ValueError) {
            $caughtException = true;
        }
        self::assertTrue($caughtException);

        $a = new TestArrayObjectExtended([10]);
        self::assertSame(10, $a->min());

        $a = new TestArrayObjectExtended([10, 5, 20]);
        self::assertSame(5, $a->min());
        self::assertSame(0, $a->getChangedCount());
    }

    /**
     * Test the natCaseSort(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_natcasesort_method(): void
    {
        $a = new TestArrayObjectExtended([1 => 'file 1', 11 => 'FILE 11', 2 => 'file 2', 10 => 'file 10']);
        self::assertTrue($a->natCaseSort());
        self::assertSame(
            [1 => 'file 1', 2 => 'file 2', 10 => 'file 10', 11 => 'FILE 11'],
            $a->getArrayCopy()
        ); // changed
        self::assertSame(1, $a->getChangedCount());

        // apply again
        self::assertTrue($a->natCaseSort());
        self::assertSame(
            [1 => 'file 1', 2 => 'file 2', 10 => 'file 10', 11 => 'FILE 11'],
            $a->getArrayCopy()
        ); // changed
        self::assertSame(2, $a->getChangedCount());
    }

    /**
     * Test the natSort(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_natsort_method(): void
    {
        $a = new TestArrayObjectExtended([1 => 'file 1', 11 => 'FILE 11', 2 => 'file 2', 10 => 'file 10']);
        self::assertTrue($a->natSort());
        self::assertSame(
            [11 => 'FILE 11', 1 => 'file 1', 2 => 'file 2', 10 => 'file 10'],
            $a->getArrayCopy() // changed
        );
        self::assertSame(1, $a->getChangedCount());

        // apply again
        self::assertTrue($a->natSort());
        self::assertSame(
            [11 => 'FILE 11', 1 => 'file 1', 2 => 'file 2', 10 => 'file 10'],
            $a->getArrayCopy()
        ); // changed
        self::assertSame(2, $a->getChangedCount());
    }

    /**
     * Test the offsetSet(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_offset_set_method(): void
    {
        $a = new TestArrayObjectExtended([]);

        self::assertSame(0, count($a));

        $a[10] = 'ten';
        self::assertSame(1, count($a));
        self::assertSame([10 => 'ten'], $a->getArrayCopy());
        self::assertSame(1, $a->getChangedCount());

        // apply again
        $a[10] = 'ten';
        self::assertSame(1, count($a));
        self::assertSame([10 => 'ten'], $a->getArrayCopy());
        self::assertSame(2, $a->getChangedCount());

        $a[11] = 'eleven';
        self::assertSame(2, count($a));
        self::assertSame([10 => 'ten', 11 => 'eleven'], $a->getArrayCopy());
        self::assertSame(3, $a->getChangedCount());
        $a = new TestArrayObjectExtended([]);

        // perform the same, but call offsetSet(..) explicitly
        self::assertSame(0, count($a));

        $a->offsetSet(10, 'ten');
        self::assertSame(1, count($a));
        self::assertSame([10 => 'ten'], $a->getArrayCopy());
        self::assertSame(1, $a->getChangedCount());

        // apply again
        $a->offsetSet(10, 'ten');
        self::assertSame(1, count($a));
        self::assertSame([10 => 'ten'], $a->getArrayCopy());
        self::assertSame(2, $a->getChangedCount());

        $a->offsetSet(11, 'eleven');
        self::assertSame(2, count($a));
        self::assertSame([10 => 'ten', 11 => 'eleven'], $a->getArrayCopy());
        self::assertSame(3, $a->getChangedCount());
    }

    /**
     * Test the offsetUnset(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_offset_unset_method(): void
    {
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven']);

        unset($a[10]);
        self::assertSame([11 => 'eleven'], $a->getArrayCopy());
        self::assertSame(1, $a->getChangedCount());

        unset($a[11]);
        self::assertSame([], $a->getArrayCopy());
        self::assertSame(2, $a->getChangedCount());

        // apply again
        try {
            unset($a[10]);
            self::assertSame([11 => 'eleven'], $a->getArrayCopy());
            self::assertSame(3, $a->getChangedCount());
        } catch (Throwable $e) {
            // unsetting index 10 again will throw an error in older PHP's
        }

        // perform the same, but call offsetUnset(..) explicitly
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven']);

        $a->offsetUnset(10);
        self::assertSame([11 => 'eleven'], $a->getArrayCopy());
        self::assertSame(1, $a->getChangedCount());

        $a->offsetUnset(11);
        self::assertSame([], $a->getArrayCopy());
        self::assertSame(2, $a->getChangedCount());

        // apply again
        try {
            $a->offsetUnset(10);
            self::assertSame([11 => 'eleven'], $a->getArrayCopy());
            self::assertSame(3, $a->getChangedCount());
        } catch (Throwable $e) {
            // unsetting index 10 again will throw an error in older PHP's
        }
    }

    /**
     * Test the pop(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_pop_method(): void
    {
        // from an empty array
        $a = new TestArrayObjectExtended([]);
        self::assertNull($a->pop());
        self::assertSame([], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // from an array that already has one value
        $a = new TestArrayObjectExtended([10 => 'ten']);
        self::assertSame('ten', $a->pop());
        self::assertSame([], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // from an array that already has two values
        $a = new TestArrayObjectExtended(['A' => 'ten', 'B' => 'eleven']);
        self::assertSame('eleven', $a->pop());
        self::assertSame(['A' => 'ten'], $a->getArrayCopy()); // changed

        self::assertSame('ten', $a->pop());
        self::assertSame([], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());
    }

    /**
     * Test the push(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_push_method(): void
    {
        // from an empty array
        $a = new TestArrayObjectExtended([]);
        self::assertSame(1, $a->push('a'));
        self::assertSame([0 => 'a'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // from an array that already has one value - with an integer key
        $a = new TestArrayObjectExtended([10 => 'ten']);
        self::assertSame(2, $a->push('a'));
        self::assertSame([10 => 'ten', 11 => 'a'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // from an array that already has one value - with a string key
        $a = new TestArrayObjectExtended(['A' => 'ten']);
        self::assertSame(2, $a->push('a'));
        self::assertSame(['A' => 'ten', 0 => 'a'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // from an array that already has one value - with a string key - push two values
        $a = new TestArrayObjectExtended(['A' => 'ten']);
        self::assertSame(3, $a->push('a', 'b'));
        self::assertSame(['A' => 'ten', 0 => 'a', 1 => 'b'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        self::assertSame(4, $a->push('c'));
        self::assertSame(['A' => 'ten', 0 => 'a', 1 => 'b', 2 => 'c'], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());
    }

    /**
     * Test the rand(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_rand_method(): void
    {
//        // from an empty array - pick 1
//        // exception: "ValueError: array_rand(): Argument #1 ($array) cannot be empty"
//        $a = new TestArrayObject([]);
//        self::assertSame(null, $a->rand());

//        // from an empty array - pick 2
//        // exception: "ValueError: array_rand(): Argument #1 ($array) cannot be empty"
//        $a = new TestArrayObject([]);
//        self::assertSame([], $a->rand());

        // with one possible value - pick 1
        $a = new TestArrayObjectExtended([10 => 'ten']);
        self::assertSame(10, $a->rand());

//        // with one possible value - pick 2
//        // exception: "ValueError: array_rand(): Argument #2 ($num) must be between 1 and the number of elements in
//        // argument #1 ($array)"
//        $a = new TestArrayObject([10 => 'ten']);
//        self::assertSame([10], $a->rand(2));

        // with many possible values - pick 1
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven', 12 => 'twelve']);
        self::assertTrue(in_array($a->rand(), $a->keys()));

        // with many possible values - pick 2
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven', 12 => 'twelve']);
        $keys = $a->rand(2);
        self::assertSame(2, count($keys));
        foreach ($keys as $key) {
            self::assertTrue(in_array($key, $a->keys()));
        }

        // check there's no change to the array
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven', 12 => 'twelve']);
        $a->rand();
        self::assertSame([10 => 'ten', 11 => 'eleven', 12 => 'twelve'], $a->getArrayCopy());

        self::assertSame(0, $a->getChangedCount());
    }

    /**
     * Test the reverse(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_reverse_method(): void
    {
        // preserve keys
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven']);
        $a->reverse(true);
        self::assertSame([11 => 'eleven', 10 => 'ten'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // apply again
        $a->reverse(true);
        self::assertSame([10 => 'ten', 11 => 'eleven'], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());

        // don't preserve keys
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven']);
        $a->reverse();
        self::assertSame([0 => 'eleven', 1 => 'ten'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // apply again
        $a->reverse();
        self::assertSame([0 => 'ten', 1 => 'eleven'], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());

        // don't preserve keys - explicitly
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven']);
        $a->reverse(false);
        self::assertSame([0 => 'eleven', 1 => 'ten'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // apply again
        $a->reverse(false);
        self::assertSame([0 => 'ten', 1 => 'eleven'], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());
    }

    /**
     * Test the rNatCaseSort(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_rnatcasesort_method(): void
    {
        $a = new TestArrayObjectExtended([1 => 'file 1', 11 => 'FILE 11', 2 => 'file 2', 10 => 'file 10']);
        self::assertTrue($a->rNatCaseSort());
        self::assertSame(
            [11 => 'FILE 11', 10 => 'file 10', 2 => 'file 2', 1 => 'file 1'],
            $a->getArrayCopy()
        ); // changed
        self::assertSame(1, $a->getChangedCount());

        // apply again
        self::assertTrue($a->rNatCaseSort());
        self::assertSame(
            [11 => 'FILE 11', 10 => 'file 10', 2 => 'file 2', 1 => 'file 1'],
            $a->getArrayCopy()
        ); // changed
        self::assertSame(2, $a->getChangedCount());
    }

    /**
     * Test the rNatSort(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_rnatsort_method(): void
    {
        $a = new TestArrayObjectExtended([1 => 'file 1', 11 => 'FILE 11', 2 => 'file 2', 10 => 'file 10']);
        self::assertTrue($a->rNatSort());
        self::assertSame(
            [10 => 'file 10', 2 => 'file 2', 1 => 'file 1', 11 => 'FILE 11'],
            $a->getArrayCopy()
        ); // changed
        self::assertSame(1, $a->getChangedCount());

        // apply again
        self::assertTrue($a->rNatSort());
        self::assertSame(
            [10 => 'file 10', 2 => 'file 2', 1 => 'file 1', 11 => 'FILE 11'],
            $a->getArrayCopy()
        ); // changed
        self::assertSame(2, $a->getChangedCount());
    }

    /**
     * Test the rSort(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_rsort_method(): void
    {
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven', 12 => 'twelve']);
        self::assertTrue($a->rSort());
        self::assertSame([0 => 'twelve', 1 => 'ten', 2 => 'eleven'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // apply again
        self::assertTrue($a->rSort());
        self::assertSame([0 => 'twelve', 1 => 'ten', 2 => 'eleven'], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());

        // check that sorting flag is passed through
        $a = new TestArrayObjectExtended([1 => 1, 2 => 2, 11 => 11, '12' => '12']);
        self::assertTrue($a->rSort(SORT_STRING));
        self::assertSame([0 => 2, 1 => '12', 2 => 11, 3 => 1], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        self::assertTrue($a->rSort(SORT_NUMERIC));
        self::assertSame([0 => '12', 1 => 11, 2 => 2, 3 => 1], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());
    }

    /**
     * Test the search(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_search_method(): void
    {
        // from an empty array
        $a = new TestArrayObjectExtended([]);
        self::assertFalse($a->search('zero'));

        $start = [1 => 1, 10 => 'ten', 11 => 'eleven', 12 => 'twelve', 2 => true];
        $a = new TestArrayObjectExtended($start);
        self::assertSame(10, $a->search('ten'));
        self::assertSame(11, $a->search('eleven'));
        self::assertSame(12, $a->search('twelve'));
        self::assertSame(1, $a->search(true));
        self::assertSame(1, $a->search(true, false));
        self::assertSame(2, $a->search(true, true));
        self::assertSame(2, $a->search('zero'));
        self::assertSame(2, $a->search('zero', false));
        self::assertFalse($a->search('zero', true));

        self::assertSame($start, $a->getArrayCopy()); // no change

        self::assertSame(0, $a->getChangedCount());
    }

    /**
     * Test the shift(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_shift_method(): void
    {
        // from an empty array
        $a = new TestArrayObjectExtended([]);
        self::assertNull($a->shift());
        self::assertSame([], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // from an array that already has one value
        $a = new TestArrayObjectExtended([10 => 'ten']);
        self::assertSame('ten', $a->shift());
        self::assertSame([], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // from an array that already has two values
        $a = new TestArrayObjectExtended(['A' => 'ten', 'B' => 'eleven']);
        self::assertSame('ten', $a->shift());
        self::assertSame(['B' => 'eleven'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        self::assertSame('eleven', $a->shift());
        self::assertSame([], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());
    }

    /**
     * Test the shuffle(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_shuffle_method(): void
    {
        $size = 1000;
        $orig = range(1, $size);

        $a = new TestArrayObjectExtended($orig);
        $a->shuffle();

        $new = $a->getArrayCopy();
        $sortedAgain = $a->getArrayCopy();
        sort($sortedAgain);

        self::assertSame($size, count($new));
        self::assertSame(1, min($new));
        self::assertSame($size, max($new));
        self::assertNotSame($orig, $new); // this has an infinitesimally chance of being the same
        self::assertSame($orig, $sortedAgain);
        self::assertSame(1, $a->getChangedCount());
    }

    /**
     * Test the slice(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_slice_method(): void
    {
        // from an empty array
        $a = new TestArrayObjectExtended([]);
        self::assertSame([], $a->slice(0, 1));

        // from an array containing 3 values
        $start = [10 => 'ten', 11 => 'eleven', 12 => 'twelve'];
        $a = new TestArrayObjectExtended($start);

        // $offset [-3 to 3], no $length, no $preserveKeys
        self::assertSame([0 => 'ten', 1 => 'eleven', 2 => 'twelve'], $a->slice(-3));
        self::assertSame([0 => 'eleven', 1 => 'twelve'], $a->slice(-2));
        self::assertSame([0 => 'twelve'], $a->slice(-1));
        self::assertSame([0 => 'ten', 1 => 'eleven', 2 => 'twelve'], $a->slice(0));
        self::assertSame([0 => 'eleven', 1 => 'twelve'], $a->slice(1));
        self::assertSame([0 => 'twelve'], $a->slice(2));
        self::assertSame([], $a->slice(3));

        // $offset -3, $length [-3 to 3], no $preserveKeys
        self::assertSame([], $a->slice(-3, -4));
        self::assertSame([], $a->slice(-3, -3));
        self::assertSame([0 => 'ten'], $a->slice(-3, -2));
        self::assertSame([0 => 'ten', 1 => 'eleven'], $a->slice(-3, -1));
        self::assertSame([], $a->slice(-3, 0));
        self::assertSame([0 => 'ten'], $a->slice(-3, 1));
        self::assertSame([0 => 'ten', 1 => 'eleven'], $a->slice(-3, 2));
        self::assertSame([0 => 'ten', 1 => 'eleven', 2 => 'twelve'], $a->slice(-3, 3));
        self::assertSame([0 => 'ten', 1 => 'eleven', 2 => 'twelve'], $a->slice(-3, 4));

        // $offset -2, $length [-3 to 3], no $preserveKeys
        self::assertSame([], $a->slice(-2, -4));
        self::assertSame([], $a->slice(-2, -3));
        self::assertSame([], $a->slice(-2, -2));
        self::assertSame([0 => 'eleven'], $a->slice(-2, -1));
        self::assertSame([], $a->slice(-2, 0));
        self::assertSame([0 => 'eleven'], $a->slice(-2, 1));
        self::assertSame([0 => 'eleven', 1 => 'twelve'], $a->slice(-2, 2));
        self::assertSame([0 => 'eleven', 1 => 'twelve'], $a->slice(-2, 3));
        self::assertSame([0 => 'eleven', 1 => 'twelve'], $a->slice(-2, 4));

        // $offset -1, $length [-3 to 3], no $preserveKeys
        self::assertSame([], $a->slice(-1, -4));
        self::assertSame([], $a->slice(-1, -3));
        self::assertSame([], $a->slice(-1, -2));
        self::assertSame([], $a->slice(-1, -1));
        self::assertSame([], $a->slice(-1, 0));
        self::assertSame([0 => 'twelve'], $a->slice(-1, 1));
        self::assertSame([0 => 'twelve'], $a->slice(-1, 2));
        self::assertSame([0 => 'twelve'], $a->slice(-1, 3));
        self::assertSame([0 => 'twelve'], $a->slice(-1, 4));

        // $offset 0, $length [-3 to 3], no $preserveKeys
        self::assertSame([], $a->slice(0, -4));
        self::assertSame([], $a->slice(0, -3));
        self::assertSame([0 => 'ten'], $a->slice(0, -2));
        self::assertSame([0 => 'ten', 1 => 'eleven'], $a->slice(0, -1));
        self::assertSame([], $a->slice(0, 0));
        self::assertSame([0 => 'ten'], $a->slice(0, 1));
        self::assertSame([0 => 'ten', 1 => 'eleven'], $a->slice(0, 2));
        self::assertSame([0 => 'ten', 1 => 'eleven', 2 => 'twelve'], $a->slice(0, 3));
        self::assertSame([0 => 'ten', 1 => 'eleven', 2 => 'twelve'], $a->slice(0, 4));

        // $offset 1, $length [-3 to 3], no $preserveKeys
        self::assertSame([], $a->slice(1, -4));
        self::assertSame([], $a->slice(1, -3));
        self::assertSame([], $a->slice(1, -2));
        self::assertSame([0 => 'eleven'], $a->slice(1, -1));
        self::assertSame([], $a->slice(1, 0));
        self::assertSame([0 => 'eleven'], $a->slice(1, 1));
        self::assertSame([0 => 'eleven', 1 => 'twelve'], $a->slice(1, 2));
        self::assertSame([0 => 'eleven', 1 => 'twelve'], $a->slice(1, 3));
        self::assertSame([0 => 'eleven', 1 => 'twelve'], $a->slice(1, 4));

        // $offset 2, $length [-3 to 3], no $preserveKeys
        self::assertSame([], $a->slice(2, -4));
        self::assertSame([], $a->slice(2, -3));
        self::assertSame([], $a->slice(2, -2));
        self::assertSame([], $a->slice(2, -1));
        self::assertSame([], $a->slice(2, 0));
        self::assertSame([0 => 'twelve'], $a->slice(2, 1));
        self::assertSame([0 => 'twelve'], $a->slice(2, 2));
        self::assertSame([0 => 'twelve'], $a->slice(2, 3));
        self::assertSame([0 => 'twelve'], $a->slice(2, 4));

        // $offset 3, $length [-3 to 3], no $preserveKeys
        self::assertSame([], $a->slice(3, -4));
        self::assertSame([], $a->slice(3, -3));
        self::assertSame([], $a->slice(3, -2));
        self::assertSame([], $a->slice(3, -1));
        self::assertSame([], $a->slice(3, 0));
        self::assertSame([], $a->slice(3, 1));
        self::assertSame([], $a->slice(3, 2));
        self::assertSame([], $a->slice(3, 3));
        self::assertSame([], $a->slice(3, 4));

        // $offset 0, $length [-3 to 3], $preserveKeys = false
        self::assertSame([], $a->slice(0, -4, false));
        self::assertSame([], $a->slice(0, -3, false));
        self::assertSame([0 => 'ten'], $a->slice(0, -2, false));
        self::assertSame([0 => 'ten', 1 => 'eleven'], $a->slice(0, -1, false));
        self::assertSame([], $a->slice(0, 0, false));
        self::assertSame([0 => 'ten'], $a->slice(0, 1, false));
        self::assertSame([0 => 'ten', 1 => 'eleven'], $a->slice(0, 2, false));
        self::assertSame([0 => 'ten', 1 => 'eleven', 2 => 'twelve'], $a->slice(0, 3, false));
        self::assertSame([0 => 'ten', 1 => 'eleven', 2 => 'twelve'], $a->slice(0, 4, false));

        // $offset 0, $length [-3 to 3], $preserveKeys = true
        self::assertSame([], $a->slice(0, -4, true));
        self::assertSame([], $a->slice(0, -3, true));
        self::assertSame([10 => 'ten'], $a->slice(0, -2, true));
        self::assertSame([10 => 'ten', 11 => 'eleven'], $a->slice(0, -1, true));
        self::assertSame([], $a->slice(0, 0, true));
        self::assertSame([10 => 'ten'], $a->slice(0, 1, true));
        self::assertSame([10 => 'ten', 11 => 'eleven'], $a->slice(0, 2, true));
        self::assertSame([10 => 'ten', 11 => 'eleven', 12 => 'twelve'], $a->slice(0, 3, true));
        self::assertSame([10 => 'ten', 11 => 'eleven', 12 => 'twelve'], $a->slice(0, 4, true));

        self::assertSame($start, $a->getArrayCopy()); // didn't change

        self::assertSame(0, $a->getChangedCount());
    }

    /**
     * Test the sort(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_sort_method(): void
    {
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven', 12 => 'twelve']);
        self::assertTrue($a->sort());
        self::assertSame([0 => 'eleven', 1 => 'ten', 2 => 'twelve'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // apply again
        self::assertTrue($a->sort());
        self::assertSame([0 => 'eleven', 1 => 'ten', 2 => 'twelve'], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());

        // check that sorting flag is passed through
        $a = new TestArrayObjectExtended([1 => 1, 2 => 2, 11 => 11, '12' => '12']);
        self::assertTrue($a->sort(SORT_STRING));
        self::assertSame([0 => 1, 1 => 11, 2 => '12', 3 => 2], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        self::assertTrue($a->sort(SORT_NUMERIC));
        self::assertSame([0 => 1, 1 => 2, 2 => 11, 3 => '12'], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());
    }

    /**
     * Test the uASort(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_uasort_method(): void
    {
        $callback = fn($a, $b) => $a > $b ? 1 : -1;

        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven', 12 => 'twelve']);
        self::assertTrue($a->uASort($callback));
        self::assertSame([11 => 'eleven', 10 => 'ten', 12 => 'twelve'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // apply again
        self::assertTrue($a->uASort($callback));
        self::assertSame([11 => 'eleven', 10 => 'ten', 12 => 'twelve'], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());
    }

    /**
     * Test the uKSort(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_uksort_method(): void
    {
        $callback = fn($a, $b) => $a > $b ? 1 : -1;

        $a = new TestArrayObjectExtended([10 => 'ten', 12 => 'twelve', 11 => 'eleven']);
        self::assertTrue($a->uKSort($callback));
        self::assertSame([10 => 'ten', 11 => 'eleven', 12 => 'twelve'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // apply again
        self::assertTrue($a->uKSort($callback));
        self::assertSame([10 => 'ten', 11 => 'eleven', 12 => 'twelve'], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());
    }

    /**
     * Test the unique(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_unique_method(): void
    {
        // from an empty array
        $a = new TestArrayObjectExtended([]);
        self::assertSame([], $a->unique());

        $a = new TestArrayObjectExtended([10 => 'ten']);
        self::assertSame([10 => 'ten'], $a->unique());

        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'ten']);
        self::assertSame([10 => 'ten'], $a->unique());

        $a = new TestArrayObjectExtended(['A' => 'ten', 'B' => 'ten', 'eleven']);
        self::assertSame(['A' => 'ten', 0 => 'eleven'], $a->unique());

        // check that sorting flag is passed through
        $a = new TestArrayObjectExtended(['10.100', 10.1]);
        self::assertSame(['10.100'], $a->unique(SORT_NUMERIC));

        $a = new TestArrayObjectExtended(['10.100', 10.1]);
        self::assertSame(['10.100', 10.1], $a->unique(SORT_STRING));

        self::assertSame(['10.100', 10.1], $a->getArrayCopy()); // didn't change

        self::assertSame(0, $a->getChangedCount());
    }

    /**
     * Test the serialize(..) and unserialize(..) methods.
     *
     * @test
     *
     * @return void
     */
    public static function test_serialization_methods(): void
    {
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven']);

        $b = new TestArrayObjectExtended();
        $b->unserialize($a->serialize());

        self::assertEquals($a, $b);
        self::assertSame(0, $a->getChangedCount());
        self::assertSame(1, $b->getChangedCount());
    }

    /**
     * Test the unshift(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_unshift_method(): void
    {
        // from an empty array
        $a = new TestArrayObjectExtended([]);
        self::assertSame(1, $a->unshift('a'));
        self::assertSame([0 => 'a'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // from an array that already has one value - with an integer key
        $a = new TestArrayObjectExtended([10 => 'ten']);
        self::assertSame(2, $a->unshift('a'));
        self::assertSame([0 => 'a', 1 => 'ten'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // from an array that already has one value - with a string key
        $a = new TestArrayObjectExtended(['A' => 'ten']);
        self::assertSame(2, $a->unshift('a'));
        self::assertSame([0 => 'a', 'A' => 'ten'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // from an array that already has one value - with a string key - unshift two values
        $a = new TestArrayObjectExtended(['A' => 'ten']);
        self::assertSame(3, $a->unshift('a', 'b'));
        self::assertSame([0 => 'a', 1 => 'b', 'A' => 'ten'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        self::assertSame(4, $a->unshift('c'));
        self::assertSame([0 => 'c', 1 => 'a', 2 => 'b', 'A' => 'ten'], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());
    }

    /**
     * Test the uSort(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_usort_method(): void
    {
        $callback = fn($a, $b) => $a > $b ? 1 : -1;

        $a = new TestArrayObjectExtended([10 => 'ten', 12 => 'twelve', 11 => 'eleven']);
        self::assertTrue($a->uSort($callback));
        self::assertSame([0 => 'eleven', 1 => 'ten', 2 => 'twelve'], $a->getArrayCopy()); // changed
        self::assertSame(1, $a->getChangedCount());

        // apply again
        self::assertTrue($a->uSort($callback));
        self::assertSame([0 => 'eleven', 1 => 'ten', 2 => 'twelve'], $a->getArrayCopy()); // changed
        self::assertSame(2, $a->getChangedCount());
    }

    /**
     * Test the values(..) method.
     *
     * @test
     *
     * @return void
     */
    public static function test_values_method(): void
    {
        $a = new TestArrayObjectExtended([10 => 'ten', 11 => 'eleven', 12 => 'twelve']);

        self::assertSame(['ten', 'eleven', 'twelve'], $a->values());
        self::assertSame([10 => 'ten', 11 => 'eleven', 12 => 'twelve'], $a->getArrayCopy()); // didn't change
        self::assertSame(0, $a->getChangedCount());
    }





    /**
     * Test the key(), current(), prev(), next(), reset() and end() methods.
     *
     * @test
     *
     * @return void
     */
    public static function test_array_iterator_methods(): void
    {
        $origArray = [100 => 'a', 101 => 'b', 102 => 'c'];

        // test PHP's function version of the methods
        $array = $origArray;
        self::assertSame('a', current($array));
        self::assertSame(100, key($array));

        self::assertSame('b', next($array));
        self::assertSame('b', current($array));
        self::assertSame(101, key($array));

        self::assertSame('c', next($array));
        self::assertSame('c', current($array));
        self::assertSame(102, key($array));

        self::assertSame(false, next($array));
        self::assertSame(false, current($array));
        self::assertSame(null, key($array));

        self::assertSame('a', reset($array));
        self::assertSame('a', current($array));
        self::assertSame(100, key($array));

        self::assertSame('b', next($array));
        self::assertSame('b', current($array));
        self::assertSame(101, key($array));

        self::assertSame('a', prev($array));
        self::assertSame('a', current($array));
        self::assertSame(100, key($array));

        self::assertSame(false, prev($array));
        self::assertSame(false, current($array));
        self::assertSame(null, key($array));

        self::assertSame('c', end($array));
        self::assertSame('c', current($array));
        self::assertSame(102, key($array));



        // test the ArrayObjectExtended implementation of the methods
        $arrayObject = new TestArrayObjectExtended($origArray);
        self::assertSame('a', $arrayObject->current());
        self::assertSame(100, $arrayObject->key());

        self::assertSame('b', $arrayObject->next());
        self::assertSame('b', $arrayObject->current());
        self::assertSame(101, $arrayObject->key());

        self::assertSame('c', $arrayObject->next());
        self::assertSame('c', $arrayObject->current());
        self::assertSame(102, $arrayObject->key());

        self::assertSame(false, $arrayObject->next());
        self::assertSame(false, $arrayObject->current());
        self::assertSame(null, $arrayObject->key());

        self::assertSame('a', $arrayObject->reset());
        self::assertSame('a', $arrayObject->current());
        self::assertSame(100, $arrayObject->key());

        self::assertSame('b', $arrayObject->next());
        self::assertSame('b', $arrayObject->current());
        self::assertSame(101, $arrayObject->key());

        self::assertSame('a', $arrayObject->prev());
        self::assertSame('a', $arrayObject->current());
        self::assertSame(100, $arrayObject->key());

        self::assertSame(false, $arrayObject->prev());
        self::assertSame(false, $arrayObject->current());
        self::assertSame(null, $arrayObject->key());

        self::assertSame('c', $arrayObject->end());
        self::assertSame('c', $arrayObject->current());
        self::assertSame(102, $arrayObject->key());

        self::assertSame(0, $arrayObject->getChangedCount());
    }





    /**
     * Test which methods reset the array pointer when called.
     *
     * @test
     * @dataProvider resetArrayPointerMethodsDataProvider
     *
     * @param string                $method   The method to test.
     * @param string                $function PHP's equivalent function.
     * @param array<integer, mixed> $args     The arguments to pass to the method.
     * @param boolean               $reset    Whether the current index should be reset.
     * @return void
     */
    public static function test_methods_that_reset_the_array_pointer(
        string $method,
        string $function,
        array $args,
        bool $reset
    ): void {

        $origArray = [101 => 'b', 100 => 'a', 103 => 'd', 102 => 'c'];

//var_dump($method);

        // test PHP's function version of the method
        $array = $origArray;
        end($array);
        $keyBefore = key($array);
        /** @var callable $function */
//var_dump($array); // before
        call_user_func_array($function, array_merge([&$array], $args));
        $keyAfter = key($array);
//var_dump($array); // after
//print "$keyBefore $keyAfter\n"; // keys
//var_dump($keyBefore); // current key before
//var_dump($keyAfter); // current key after
        self::assertSame(!$reset, $keyBefore === $keyAfter);

        $phpFunctionKeyAfter = $keyAfter;

        // test the ArrayObjectExtended implementation of the method
        $arrayObject = new TestArrayObjectExtended($origArray);
        $arrayObject->end();
//var_dump($arrayObject); // before
        $keyBefore = $arrayObject->key();
        /** @var callable $callable */
        $callable = [$arrayObject, $method];
        call_user_func_array($callable, array_merge($args));
        $keyAfter = $arrayObject->key();
//var_dump($arrayObject); // after
//print "$keyBefore $keyAfter\n"; // keys
//var_dump($keyBefore); // current key before
//var_dump($keyAfter); // current key after
        self::assertSame($phpFunctionKeyAfter, $keyAfter);
    }

    /**
     * DataProvider for test_methods_that_reset_the_array_pointer().
     *
     * @return array<integer, array<string, array<integer, callable>|boolean|string>>
     */
    public static function resetArrayPointerMethodsDataProvider(): array
    {
        $sortCallback = fn($val1, $val2) => $val1 >= $val2 ? 1 : 0;

        // list taken from https://www.php.net/manual/en/function.array-walk.php

        return [
//            ['mthd' => 'array_change_key_case', 'fn' => 'array_change_key_case', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_chunk', 'fn' => 'array_chunk', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_column', 'fn' => 'array_column', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_combine', 'fn' => 'array_combine', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_count_values', 'fn' => 'array_count_values', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_diff_assoc', 'fn' => 'array_diff_assoc', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_diff_key', 'fn' => 'array_diff_key', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_diff_uassoc', 'fn' => 'array_diff_uassoc', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_diff_ukey', 'fn' => 'array_diff_ukey', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_diff', 'fn' => 'array_diff', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_fill_keys', 'fn' => 'array_fill_keys', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_fill', 'fn' => 'array_fill', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_filter', 'fn' => 'array_filter', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_flip', 'fn' => 'array_flip', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_intersect_assoc', 'fn' => 'array_intersect_assoc', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_intersect_key', 'fn' => 'array_intersect_key', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_intersect_uassoc', 'fn' => 'array_intersect_uassoc', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_intersect_ukey', 'fn' => 'array_intersect_ukey', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_intersect', 'fn' => 'array_intersect', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_is_list', 'fn' => 'array_is_list', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_key_exists', 'fn' => 'array_key_exists', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_key_first', 'fn' => 'array_key_first', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_key_last', 'fn' => 'array_key_last', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_keys', 'fn' => 'array_keys', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_map', 'fn' => 'array_map', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_merge_recursive', 'fn' => 'array_merge_recursive', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_merge', 'fn' => 'array_merge', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_multisort', 'fn' => 'array_multisort', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_pad', 'fn' => 'array_pad', 'args' => [], 'reset' => false],
            ['mthd' => 'pop', 'fn' => 'array_pop', 'args' => [], 'reset' => true],
//            ['mthd' => 'array_product', 'fn' => 'array_product', 'args' => [], 'reset' => false],
            ['mthd' => 'push', 'fn' => 'array_push', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_rand', 'fn' => 'array_rand', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_reduce', 'fn' => 'array_reduce', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_replace_recursive', 'fn' => 'array_replace_recursive', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_replace', 'fn' => 'array_replace', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_reverse', 'fn' => 'array_reverse', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_search', 'fn' => 'array_search', 'args' => [], 'reset' => false],
            ['mthd' => 'shift', 'fn' => 'array_shift', 'args' => [], 'reset' => true],
//            ['mthd' => 'array_slice', 'fn' => 'array_slice', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_splice', 'fn' => 'array_splice', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_sum', 'fn' => 'array_sum', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_udiff_assoc', 'fn' => 'array_udiff_assoc', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_udiff_uassoc', 'fn' => 'array_udiff_uassoc', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_udiff', 'fn' => 'array_udiff', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_uintersect_assoc', 'fn' => 'array_uintersect_assoc', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_uintersect_uassoc', 'fn' => 'array_uintersect_uassoc', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_uintersect', 'fn' => 'array_uintersect', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_unique', 'fn' => 'array_unique', 'args' => [], 'reset' => false],
            ['mthd' => 'unshift', 'fn' => 'array_unshift', 'args' => [], 'reset' => true],
//            ['mthd' => 'array_values', 'fn' => 'array_values', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_walk_recursive', 'fn' => 'array_walk_recursive', 'args' => [], 'reset' => false],
//            ['mthd' => 'array_walk', 'fn' => 'array_walk', 'args' => [], 'reset' => false],
//            ['mthd' => 'array', 'fn' => 'array', 'args' => [], 'reset' => false],
            ['mthd' => 'aRSort', 'fn' => 'arsort', 'args' => [], 'reset' => true],
            ['mthd' => 'aSort', 'fn' => 'asort', 'args' => [], 'reset' => true],
//            ['mthd' => 'compact', 'fn' => 'compact', 'args' => [], 'reset' => false],
//            ['mthd' => 'count', 'fn' => 'count', 'args' => [], 'reset' => false],
//            ['mthd' => 'current', 'fn' => 'current', 'args' => [], 'reset' => false],
//            ['mthd' => 'each', 'fn' => 'each', 'args' => [], 'reset' => false],
//            ['mthd' => 'end', 'fn' => 'end', 'args' => [], 'reset' => false],
//            ['mthd' => 'extract', 'fn' => 'extract', 'args' => [], 'reset' => false],
//            ['mthd' => 'in_array', 'fn' => 'in_array', 'args' => [], 'reset' => false],
//            ['mthd' => 'key_exists', 'fn' => 'key_exists', 'args' => [], 'reset' => false],
//            ['mthd' => 'key', 'fn' => 'key', 'args' => [], 'reset' => false],
            ['mthd' => 'kRSort', 'fn' => 'krsort', 'args' => [], 'reset' => true],
            ['mthd' => 'kSort', 'fn' => 'ksort', 'args' => [], 'reset' => true],
//            ['mthd' => 'list', 'fn' => 'list', 'args' => [], 'reset' => false],
            ['mthd' => 'natCaseSort', 'fn' => 'natcasesort', 'args' => [], 'reset' => true],
            ['mthd' => 'natSort', 'fn' => 'natsort', 'args' => [], 'reset' => true],
//            ['mthd' => 'next', 'fn' => 'next', 'args' => [], 'reset' => false],
//            ['mthd' => 'pos', 'fn' => 'pos', 'args' => [], 'reset' => false],
//            ['mthd' => 'prev', 'fn' => 'prev', 'args' => [], 'reset' => false],
//            ['mthd' => 'range', 'fn' => 'range', 'args' => [], 'reset' => false],
            ['mthd' => 'reset', 'fn' => 'reset', 'args' => [], 'reset' => true],
            ['mthd' => 'rSort', 'fn' => 'rsort', 'args' => [], 'reset' => true],
            ['mthd' => 'shuffle', 'fn' => 'shuffle', 'args' => [], 'reset' => true],
//            ['mthd' => 'sizeof', 'fn' => 'sizeof', 'args' => [], 'reset' => false],
            ['mthd' => 'sort', 'fn' => 'sort', 'args' => [], 'reset' => true],
            ['mthd' => 'uASort', 'fn' => 'uasort', 'args' => [$sortCallback], 'reset' => true],
            ['mthd' => 'uKSort', 'fn' => 'uksort', 'args' => [$sortCallback], 'reset' => true],
            ['mthd' => 'uSort', 'fn' => 'usort', 'args' => [$sortCallback], 'reset' => true],
//            ['mthd' => 'max', 'fn' => 'max', 'args' => [], 'reset' => false],
//            ['mthd' => 'min', 'fn' => 'min', 'args' => [], 'reset' => false],
        ];
    }





    /**
     * Test whether each method changes the content of the array or not.
     *
     * @test
     * @dataProvider methodContentChangeDataProvider
     *
     * @param string  $method       The method to call.
     * @param mixed[] $args         The arguments to pass when calling the method.
     * @param boolean $expectChange Expect the contents to change or not.
     * @return void
     * @throws Exception Thrown if the $method can't be called.
     */
    public static function test_which_methods_change_the_contents(
        string $method,
        array $args,
        bool $expectChange,
    ): void {

        $before = [10 => 'ten', 11 => 'eleven', 12 => 'twelve', 'tWeNtY' => 20, 'zero' => 0, 'false' => false];
        $a = new TestArrayObjectExtended($before);

        $toCall = [$a, $method];
        if (!is_callable($toCall)) {
            throw new Exception("Method \"$method\" could not be called");
        }

        call_user_func_array($toCall, $args);

        $expectChange
            ? self::assertNotSame($before, $a->getArrayCopy())
            : self::assertSame($before, $a->getArrayCopy());

        self::assertSame($expectChange ? 1 : 0, $a->getChangedCount());
    }

    /**
     * DataProvider for test_which_methods_change_the_contents().
     *
     * @return array<integer, array<string, mixed>>
     */
    public static function methodContentChangeDataProvider(): array
    {
        $return = [];

        $return[] = [
            'method' => 'append',
            'args' => [10],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'aRSort',
            'args' => [],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'aSort',
            'args' => [],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'changeKeyCase',
            'args' => [],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'chunk',
            'args' => [1],
            'expectChange' => false,
        ];

        $return[] = [
            'method' => 'contains',
            'args' => [1],
            'expectChange' => false,
        ];

        $return[] = [
            'method' => 'exchangeArray',
            'args' => [[2, 3, 4]],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'filter',
            'args' => [],
            'expectChange' => false,
        ];

        $return[] = [
            'method' => 'inArray',
            'args' => [1],
            'expectChange' => false,
        ];

        $return[] = [
            'method' => 'isList',
            'args' => [],
            'expectChange' => false,
        ];

        $return[] = [
            'method' => 'keyExists',
            'args' => [10],
            'expectChange' => false,
        ];

        $return[] = [
            'method' => 'keyFirst',
            'args' => [],
            'expectChange' => false,
        ];

        $return[] = [
            'method' => 'keyLast',
            'args' => [],
            'expectChange' => false,
        ];

        $return[] = [
            'method' => 'keys',
            'args' => [],
            'expectChange' => false,
        ];

        $return[] = [
            'method' => 'kRSort',
            'args' => [],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'kSort',
            'args' => [],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'natCaseSort',
            'args' => [],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'natSort',
            'args' => [],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'offsetSet',
            'args' => [1, 11],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'offsetUnset',
            'args' => [10],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'pop',
            'args' => [],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'push',
            'args' => ['a'],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'rand',
            'args' => [],
            'expectChange' => false,
        ];

        $return[] = [
            'method' => 'reverse',
            'args' => [],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'rNatCaseSort',
            'args' => [],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'rNatSort',
            'args' => [],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'rsort',
            'args' => [],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'search',
            'args' => ['ten'],
            'expectChange' => false,
        ];

        $return[] = [
            'method' => 'shift',
            'args' => [],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'slice',
            'args' => [1, 1],
            'expectChange' => false,
        ];

        $return[] = [
            'method' => 'sort',
            'args' => [],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'uASort',
            'args' => [fn($a, $b) => $a > $b ? 1 : -1],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'uKSort',
            'args' => [fn($a, $b) => $a > $b ? 1 : -1],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'unique',
            'args' => [],
            'expectChange' => false,
        ];

        $return[] = [
            'method' => 'unserialize',
            'args' => [(new \ArrayObject(['abc']))->serialize()],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'unshift',
            'args' => ['a'],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'uSort',
            'args' => [fn($a, $b) => $a > $b ? 1 : -1],
            'expectChange' => true,
        ];

        $return[] = [
            'method' => 'values',
            'args' => [],
            'expectChange' => false,
        ];

        return $return;
    }
}
