<?php
namespace rtens\collections;

use rtens\collections\events\ListCreateEvent;
use rtens\collections\events\ListDeleteEvent;

/**
 * A number of elements in sequential order.
 */
class Liste extends Collection {

    static $CLASSNAME = __CLASS__;

    public function __construct(array $elements = array()) {
        parent::__construct(array_values($elements));
    }

    /**
     * @param int $index
     * @throws \InvalidArgumentException
     * @return mixed Item at given index
     */
    public function get($index) {
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
     * @return void
     */
    public function append($element) {
        $this->elements[] = $element;
        $this->fire(new ListCreateEvent($element, $this->count() - 1));
    }

    /**
     * Inserts the given element to the beginning of the list.
     *
     * @param mixed $element
     * @return void
     */
    public function unshift($element) {
        $this->insert($element, 0);
        $this->fire(new ListCreateEvent($element, 0));
    }

    /**
     * Inserts the given element as the given index.
     *
     * The element currently at index will be moved forward.
     *
     * @param mixed $element
     * @param int $index
     * @return void
     */
    public function insert($element, $index) {
        array_splice($this->elements, $index, 0, array($element));
        $this->clean();
        $this->fire(new ListCreateEvent($element, $index));
    }

    public function insertAll(Liste $list, $index) {
        array_splice($this->elements, $index, 0, $list->elements);
        $this->clean();
        foreach ($list->elements as $i => $element) {
            $this->fire(new ListCreateEvent($element, $index + $i));
        }
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
     * @return \rtens\collections\Liste
     */
    public function copy() {
        return new Liste($this->elements);
    }

    private function clean() {
        $this->elements = array_values($this->elements);
    }

    /**
     * @static
     * @param string $separator
     * @param string $string
     * @return \rtens\collections\Liste
     */
    public static function split($separator, $string) {
        return new Liste(explode($separator, $string));
    }
}