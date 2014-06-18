<?php
namespace MD\Foundation\Tests\TestFixtures;

use MD\Foundation\MagicObject;

class MagicObjectClass extends MagicObject
{

    protected $ipsum;

    public function setIpsum($ipsum) {
        $this->ipsum = $ipsum;
    }

    public function getIpsum() {
        return $this->ipsum;
    }

}