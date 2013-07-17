<?php
namespace MD\Foundation\Tests\TestFixtures;

class Collection implements \Countable
{

    protected $items = array();

    public function __construct(array $items = array()) {
        $this->items = $items;
    }

    public function add($item) {
        $this->items[] = $item;
    }

    public function getAll() {
        return $this->items;
    }

    public function toArray() {
        return $this->items;
    }

    public function count() {
        return count($this->items);
    }

}