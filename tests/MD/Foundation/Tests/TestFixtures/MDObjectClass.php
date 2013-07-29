<?php
namespace MD\Foundation\Tests\TestFixtures;

use MD\Foundation\MDObject;

class MDObjectClass extends MDObject
{

    protected $ipsum;

    public function setIpsum($ipsum) {
        $this->ipsum = $ipsum;
    }

    public function getIpsum() {
        return $this->ipsum;
    }

}