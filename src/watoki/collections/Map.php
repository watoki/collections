<?php
namespace watoki\collections;

use watoki\collections\events\MapSetEvent;
use watoki\collections\events\MapRemoveEvent;

/**
 * Contains a key-value map of elements.
 */
class Map extends Collection {

    static $CLASSNAME = __CLASS__;

    private $hashes = array();

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
     * @return mixed The value of the removed key
     */
    public function remove($key) {
        $value = $this->elements[$this->hash($key)];
        unset($this->elements[$this->hash($key)]);
        $this->fire(new MapRemoveEvent($key, $value));
        return $value;
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
        $keys = array();
        foreach (array_keys($this->elements) as $key) {
            $keys[] = $this->unhash($key);
        }
        return new Set($keys);
    }

    /**
     * @param mixed $value
     * @return mixed|null
     */
    public function keyOf($value) {
        $key = array_search($value, $this->elements);
        if ($key === false) {
            return null;
        }
        return $this->unhash($key);
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
            $hash = spl_object_hash($key);
            $this->hashes[$hash] = $key;
            return $hash;
        }

        return $key;
    }

    private function unhash($key) {
        if (array_key_exists($key, $this->hashes)) {
            return $this->hashes[$key];
        }
        return $key;
    }

    public function merge(Map $map) {
        foreach ($map->elements as $key => $value) {
            $this->set($key, $value);
        }
        return $this;
    }
}
