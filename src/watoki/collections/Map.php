<?php
namespace watoki\collections;

use watoki\collections\events\MapSetEvent;
use watoki\collections\events\MapRemoveEvent;

/**
 * Contains a key-value map of elements.
 */
class Map extends Collection {

    static $CLASSNAME = __CLASS__;

    /**
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value) {
        $this->elements[$this->hash($key)] = $value;
        $this->fire(new MapSetEvent($key, $value));
    }

    /**
     * @param mixed $key
     * @throws \InvalidArgumentException
     * @return mixed Element with given key
     */
    public function get($key) {
        $hash = $this->hash($key);
        $value = $this->elements[$hash];
        return $value;
    }

    /**
     * Removes element with given key.
     *
     * @param mixed $key
     * @return void
     */
    public function remove($key) {
        $value = $this->elements[$this->hash($key)];
        unset($this->elements[$this->hash($key)]);
        $this->fire(new MapRemoveEvent($key, $value));
    }

    /**
     * @return Set With the values of this map.
     */
    public function values() {
        return new Set($this->elements);
    }

    /**
     * @return Set With key of this map as elements.
     */
    public function keys() {
        return new Set(array_keys($this->elements));
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function has($key) {
        return array_key_exists($this->hash($key), $this->elements);
    }

    public function __get($key) {
        return $this->get($key);
    }

    public function __set($key, $value) {
        $this->set($key, $value);
    }

    /**
     * @return Collection
     */
    public function copy() {
        return new Map($this->elements);
    }

    /**
     * @param mixed $key
     * @return mixed|string Hash value of given key
     */
    private function hash($key) {
        if (is_object($key)) {
            return spl_object_hash($key);
        }

        return $key;
    }
}
