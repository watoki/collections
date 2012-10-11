<?php
namespace rtens\collections\iterator;

class ArrayIterator implements \Iterator {

    static $CLASSNAME = __CLASS__;

    /**
     * @var array
     */
    private $array;

    /**
     * @var array
     */
    private $keys;

    /**
     * @var int
     */
    private $index;

    public function __construct(array $array) {
        $this->array = $array;
        $this->keys = array_keys($array);
        $this->index = -1;
    }

    /**
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed
     */
    public function current() {
        return $this->array[$this->keys[$this->index]];
    }

    /**
     * Move forward and returns next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return mixed
     */
    public function next() {
        $this->index++;
        return $this->valid() ? $this->current() : null;
    }

    /**
     * @return mixed
     */
    public function peekNext() {
        return $this->array[$this->keys[$this->index + 1]];
    }

    /**
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed
     */
    public function key() {
        return $this->keys[$this->index];
    }

    /**
     * Checks if current position is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean Current position is valid.
     */
    public function valid() {
        return isset($this->keys[$this->index]);
    }

    /**
     * @return bool
     */
    public function hasNext() {
        return isset($this->keys[$this->index + 1]);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind() {
        $this->index = 0;
    }
}
