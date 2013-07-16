<?php
namespace MD\Foundation\Tests\TestFixtures;

class ToArrayWrongClass
{

    public $id;
    public $name;
    public $categoryId;
    public $date;
    protected $additional = 'additional protected';

    public function __construct($id, $name, $categoryId, $date) {
        $this->id = $id;
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->date = $date;
    }

    public function toArray() {
        return false;
    }
    
}