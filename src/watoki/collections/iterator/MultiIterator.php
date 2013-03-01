<?php
namespace watoki\collections\iterator;

use watoki\collections\Liste;

class MultiIterator implements \Iterator {

    static $CLASSNAME = __CLASS__;

    /**
     * @var \watoki\collections\Liste|\Iterator[]
     */
    private $iterators;

    /**
     * @var int
     */
    private $index = 0;

    function __construct() {
        $this->iterators = new Liste();
    }

    /**
     * @param \Iterator $iterator
     */
    public function add(\Iterator $iterator) {
        $this->iterators->append($iterator);
    }

    /**
     * @return \Iterator|null
     */
    public function getCurrentIterator() {
        try {
            return $this->iterators->get($this->index);
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current() {
        return $this->getCurrentIterator()->current();
    }

    /**
     * Move forward to and return the next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return mixed The next element
     */
    public function next() {
        $this->getCurrentIterator()->next();
        if (!$this->getCurrentIterator()->valid()) {
            $this->moveToNextValidIterator();
        }
        return $this->current();
    }

    /**
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return int scalar on success, integer
     * 0 on failure.
     */
    public function key() {
        return $this->getCurrentIterator()->key();
    }

    /**
     * Checks if current iterator is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid() {
        return ($this->getCurrentIterator() !== null && $this->getCurrentIterator()->valid());
    }

    /**
     * Rewinds all iterators
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind() {
        for ($this->index = 0; $this->index < $this->iterators->count(); $this->index++) {
            $this->getCurrentIterator()->rewind();
        }

        $this->index = 0;
        $this->moveToNextValidIterator();
    }

    private function moveToNextValidIterator() {
        while ($this->getCurrentIterator() && !$this->getCurrentIterator()->valid()) {
            $this->index++;
        }
    }
}
