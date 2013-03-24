<?php
namespace watoki\collections;

use watoki\collections\events\SetPutEvent;
use watoki\collections\events\SetRemoveEvent;

/**
 * Contains a unique set of elements without order.
 */
class Set extends Collection {

    static $CLASSNAME = __CLASS__;

    /**
     * Adds the given element to the set if is not yet contained in it.
     *
     * @param mixed $element
     * @return void
     */
    public function put($element) {
        if (!$this->contains($element)) {
            $this->elements[] = $element;
            $this->fire(new SetPutEvent($element));
        }
    }

    /**
     * Puts all the elements of given collection in the Set
     *
     * @param Collection $collection
     */
    public function putAll(Collection $collection) {
        foreach ($collection as $element) {
            $this->put($element);
        }
    }

    /**
     * Removes given element from the set.
     *
     * @param mixed $element
     */
    public function remove($element) {
        foreach ($this->elements as $index => $e) {
            if ($e === $element) {
                unset($this->elements[$index]);
                $this->fire(new SetRemoveEvent($element));
                return;
            }
        }
    }

    /**
     * @param mixed $element
     * @return mixed True if the set contains the given element
     */
    public function contains($element) {

        foreach ($this->elements as $e) {
            if ($e === $element) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Collection
     */
    public function copy() {
        return new Set($this->elements);
    }

    /**
     * @return Set
     */
    public function deepCopy() {
        return parent::deepCopy();
    }
}
