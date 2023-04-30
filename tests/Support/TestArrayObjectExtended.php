<?php

namespace CodeDistortion\ArrayObjectExtended\Tests\Support;

use ArrayAccess;
use CodeDistortion\ArrayObjectExtended\ArrayObjectExtended;
use Countable;
use IteratorAggregate;
use Serializable;

/**
 * Add extra functionality to PHP's ArrayObject.
 *
 * @codingStandardsIgnoreStart
 *
 * @template TKey of integer|string
 * @template TValue of mixed
 * @template-implements IteratorAggregate<TKey, TValue>
 * @template-implements ArrayAccess<TKey, TValue>
 *
 * @codingStandardsIgnoreEnd
 */
class TestArrayObjectExtended extends ArrayObjectExtended implements IteratorAggregate, ArrayAccess, Serializable, Countable // @phpcs:ignore Generic.Files.LineLength
{
    /** @var integer The number of times the content has been changed. */
    private int $changedCount = 0;



    /**
     * A hook that's called when the contents of this object has changed.
     *
     * Allow for children to override (to clear internal caches etc.).
     *
     * @return void
     */
    protected function onAfterUpdate(): void
    {
        parent::onAfterUpdate();
        $this->changedCount++;
    }

    /**
     * Get the number of times the content has been changed.
     *
     * @return integer
     */
    public function getChangedCount(): int
    {
        return $this->changedCount;
    }
}
