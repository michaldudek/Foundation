<?php
namespace MD\Foundation\Tests\TestFixtures;

class ItemClass
{

    protected $id;
    protected $name;
    protected $categoryId;
    protected $date;
    protected $additional = 'additional protected';
    public $additional_public = 'public';

    public function __construct($id, $name, $categoryId, $date) {
        $this->id = $id;
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->date = $date;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getCategoryId() {
        return $this->categoryId;
    }

    public function getDate() {
        return $this->date;
    }

    public function getAdditional() {
        return $this->additional;
    }

    public function getAdditionalPublic() {
        return $this->additional_public;
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