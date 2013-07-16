<?php
namespace MD\Foundation\Tests\TestFixtures;

class ToArrayClass
{

    protected $id;
    protected $name;
    protected $categoryId;
    protected $date;
    protected $additional = 'additional protected';
    public $additionalPublic = 'additional public';

    public function __construct($id, $name, $categoryId, $date) {
        $this->id = $id;
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->date = $date;
    }

    public function toArray() {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'categoryId' => $this->categoryId,
            'date' => $this->date
        );
    }
    
}