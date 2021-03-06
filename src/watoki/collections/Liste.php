<?php
namespace watoki\collections;

use watoki\collections\events\ListCreateEvent;
use watoki\collections\events\ListDeleteEvent;

/**
 * A number of elements in sequential order.
 */
class Liste extends Collection {

    static $CLASSNAME = __CLASS__;

    /**
     * @param array|Collection $elements
     */
    public function __construct($elements = array()) {
        parent::__construct(array_values(
                $elements instanceof Collection
                        ? $elements->elements
                        : $elements
        ));
    }

    /**
     * @param int $index
     * @throws \InvalidArgumentException
     * @return mixed Item at given index
     */
    public function get($index) {
        if ($index < 0) {
            $index = $this->count() + $index;
        }
        if (!array_key_exists($index, $this->elements)) {
            throw new \InvalidArgumentException('Index not set: ' . $index);
        }
        return $this->elements[$index];
    }

    /**
     * @return mixed First element.
     */
    public function first() {
        return $this->elements[0];
    }

    /**
     * @return mixed Last element.
     */
    public function last() {
        return $this->elements[count($this->elements) - 1];
    }

    /**
     * Adds element to end of list.
     *
     * @param mixed $element
     * @return static
     */
    public function append($element) {
        $this->elements[] = $element;
        $this->fire(new ListCreateEvent($element, $this->count() - 1));
        return $this;
    }

    /**
     * @param Collection $collection
     * @return static
     */
    public function appendAll(Collection $collection) {
        foreach ($collection->elements as $element) {
            $this->append($element);
        }
        return $this;
    }

    /**
     * Inserts the given element to the beginning of the list.
     *
     * @param mixed $element
     * @return static
     */
    public function unshift($element) {
        $this->insert($element, 0);
        $this->fire(new ListCreateEvent($element, 0));
        return $this;
    }

    /**
     * Inserts the given element as the given index.
     *
     * The element currently at index will be moved forward.
     *
     * @param mixed $element
     * @param int $index
     * @return static
     */
    public function insert($element, $index) {
        $this->insertAll(new Liste(array($element)), $index);
        return $this;
    }

    /**
     * @param Collection $list
     * @param int $index
     * @return static
     */
    public function insertAll(Collection $list, $index) {
        array_splice($this->elements, $index, 0, $list->elements);
        $this->clean();
        foreach ($list->elements as $i => $element) {
            $this->fire(new ListCreateEvent($element, $index + $i));
        }
        return $this;
    }

    /**
     * Removes and returns the element at given index from list.
     *
     * @param int $index
     * @return mixed
     */
    public function remove($index) {
        $e = $this->elements[$index];
        unset($this->elements[$index]);
        $this->clean();
        $this->fire(new ListDeleteEvent($e, $index));
        return $e;
    }

    /**
     * @param mixed $element
     * @return mixed The removed element
     */
    public function removeElement($element) {
        return $this->remove($this->indexOf($element));
    }

    /**
     * Removes and returns the last element of the list.
     *
     * @return mixed
     */
    public function pop() {
        $e = array_pop($this->elements);
        $this->fire(new ListDeleteEvent($e, $this->count()));
        return $e;
    }

    /**
     * Removes and returns the first element of the list.
     *
     * @return mixed
     */
    public function shift() {
        $e = array_shift($this->elements);
        $this->clean();
        $this->fire(new ListDeleteEvent($e, 0));
        return $e;
    }

    /**
     * @param int $start Use negative numbers to count from the end.
     * @param int|null $length Omit to include last element. User negative numbers to count from the end.
     * @return Liste
     */
    public function slice($start, $length = null) {
        $slice = array_slice($this->elements, $start, $length);
        $this->clean();
        return new Liste($slice);
    }

    /**
     * @param $start
     * @param null $length
     * @param Liste $replacement
     * @return Liste
     */
    public function splice($start, $length = null, Liste $replacement = null) {
        $retval = array_splice($this->elements, $start, $length, $replacement ? $replacement->toArray() : null);
        $this->clean();
        return new Liste($retval);
    }

    /**
     * @param $element
     * @return bool True if the list contains given element.
     */
    public function contains($element) {
        return $this->indexOf($element) > -1;
    }

    public function isInBound($index) {
        return count($this->elements) > $index;
    }

    /**
     * @param Liste $subtrahend
     * @return Liste
     */
    public function diff(Liste $subtrahend) {
        return new Liste(array_diff($this->elements, $subtrahend->elements));
    }

    /**
     * @param string $glue
     * @return string
     */
    public function join($glue) {
        return implode($glue, $this->elements);
    }

    /**
     * @param mixed $element
     * @return int Index of first occurrence of given element. -1 if not found.
     */
    public function indexOf($element) {
        foreach ($this->elements as $index => $e) {
            if ($e === $element) {
                return intval($index);
            }
        }

        return -1;
    }

    /**
     * @return static
     */
    public function copy() {
        return parent::copy();
    }

    /**
     * @return Liste
     */
    public function deepCopy() {
        return parent::deepCopy();
    }

    private function clean() {
        $this->elements = array_values($this->elements);
    }

    /**
     * @static
     * @param string $separator
     * @param string $string
     * @return \watoki\collections\Liste
     */
    public static function split($separator, $string) {
        return new Liste(explode($separator, $string));
    }
}