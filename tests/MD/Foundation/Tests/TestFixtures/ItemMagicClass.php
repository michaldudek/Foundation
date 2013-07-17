<?php
namespace MD\Foundation\Tests\TestFixtures;

class ItemMagicClass
{

    public $vars = array();

    public function __construct($id, $name, $categoryId, $date) {
        $this->vars['id'] = $id;
        $this->vars['name'] = $name;
        $this->vars['categoryId'] = $categoryId;
        $this->vars['date'] = $date;
    }

    public function __get($var) {
        return $this->vars[$var];
    }

    public function __isset($var) {
        return isset($this->vars[$var]);
    }

    public function __call($method, $args) {
        if (strtolower(substr($method, 0, 3)) === 'get') {
            $var = lcfirst(substr($method, 3));
            return $this->vars[$var];
        }
    }

}