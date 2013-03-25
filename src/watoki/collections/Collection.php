<?php
namespace watoki\collections;

use watoki\smokey\EventDispatcher;
use watoki\collections\iterator\ArrayIterator;
use watoki\collections\events\CollectionEvent;

/**
 * A bunch of elements. Base class for List, Set and Map
 */
abstract class Collection implements \Countable, \IteratorAggregate {

    static $CLASSNAME = __CLASS__;

    /**
     * @var array
     */
    protected $elements;

    /**
     * @var \watoki\smokey\EventDispatcher
     */
    private $dispatcher;

    /**
     * @static
     * @param array $array
     * @return Map
     */
    static public function toMap(array $array) {
        $collection = self::toCollections($array);
        return new Map($collection->elements);
    }

    /**
     * @static
     * @param array $array
     * @return Liste
     */
    static public function toList(array $array) {
        $collection = self::toCollections($array);
        return new Liste($collection->elements);
    }

    /**
     * Traverses public attributes of objects and arrays and converts all arrays to Lists and Maps
     *
     * @static
     * @param mixed $object
     * @return mixed
     */
    static public function toCollections($object) {
        if ($object === null) {
            return null;
        }

        if (!is_array($object)) {
            if (is_object($object)) {
                foreach ($object as $key => $value) {
                    $object->$key = self::toCollections($value);
                }
            }
            return $object;
        }

        if (empty($object)) {
            return new Map();
        }

        $elements = array();
        $isList = true;
        $lastKey = -1;

        foreach ($object as $key => $element) {
            $elements[$key] = self::toCollections($element);

            if (!is_int($key) || intval($key) != $lastKey + 1) {
                $isList = false;
            }
            $lastKey = intval($key);
        }

        if ($isList) {
            return new Liste($elements);
        } else {
            return new Map($elements);
        }
    }

    /**
     * @return array The collection as array (recursively)
     */
    public function toArray() {
        $elements = array();
        foreach ($this->elements as $key => $element) {
            if ($element instanceof Collection) {
                /** @var $element Collection */
                $element = $element->toArray();
            }
            $elements[$key] = $element;
        }
        return $elements;
    }

    /**
     * @abstract
     * @return static
     */
    public function copy() {
        return new static($this->elements);
    }

    /**
     * @return static
     */
    public function deepCopy() {
        $copy = $this->copy();
        foreach ($copy->elements as $key => $value) {
            if ($value instanceof Collection) {
                $copy->elements[$key] = $value->copy();
            }
        }
        return $copy;
    }

    public function __construct(array $elements = array()) {
        $this->elements = $elements;
    }

    protected function getDispatcher() {
        if (!$this->dispatcher) {
            $this->dispatcher = new EventDispatcher();
        }
        return $this->dispatcher;
    }

    protected function fire(CollectionEvent $event) {
        if ($this->dispatcher) {
            $this->dispatcher->fire($event);
        }
    }

    /**
     * @param string $eventName
     * @param \Closure $listener
     * @return void
     */
    public function on($eventName, \Closure $listener) {
        $this->getDispatcher()->addListener($eventName, $listener);
    }

    /**
     * Retrieve an external iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Iterator
     */
    public function getIterator() {
        return new ArrayIterator($this->elements);
    }

    /**
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     */
    public function count() {
        return count($this->elements);
    }

    /**
     * @return bool True if the list contains no elements.
     */
    public function isEmpty() {
        return $this->count() == 0;
    }

    /**
     * Clears the collection.
     */
    public function clear() {
        $this->elements = array();
    }

    /**
     * Filters all elements out that don't match the given filter.
     *
     * @param \callable $matches
     * @return \watoki\collections\Collection A new Collection with the filtered elements
     */
    public function filter($matches) {
        $filtered = array();
        foreach ($this as $element) {
            if ($matches($element)) {
                $filtered[] = $element;
            }
        }

        $copy = $this->copy();
        $copy->elements = $filtered;
        return $copy;
    }

    /**
     * @return null|mixed One element of the collection or null if collection empty
     */
    public function one() {
        if ($this->isEmpty()) {
            return null;
        }
        return reset($this->elements);
    }
}
