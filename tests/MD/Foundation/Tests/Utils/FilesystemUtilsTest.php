<?php
namespace MD\Foundation\Tests\Utils;

use MD\Foundation\Utils\FilesystemUtils;

/**
 * @coversDefaultClass \MD\Foundation\Utils\FilesystemUtils
 */
class FilesystemUtilsTest extends \PHPUnit_Framework_TestCase
{

    public function testStandardGlob() {
        $globDir = rtrim(realpath(__DIR__ .'/../TestFixtures/glob/'), '/') .'/';
        
        $this->assertEquals(array(
            $globDir .'app.js',
            $globDir .'app.min.js',
            $globDir .'global.js',
            $globDir .'global.min.js'
        ), FilesystemUtils::glob($globDir .'*.js'));

        $this->assertEquals(array(
            $globDir .'app.min.js',
            $globDir .'global.min.js'
        ), FilesystemUtils::glob($globDir .'*.min.js'));
    }

    public function testGlobStar() {
        $globDir = rtrim(realpath(__DIR__ .'/../TestFixtures/glob/'), '/') .'/';

        $this->assertEquals(array(
            $globDir .'company/bilbo.txt',
            $globDir .'company/dwarves/bifur.txt',
            $globDir .'company/dwarves/bombur.txt',
            $globDir .'company/dwarves/oin.txt',
            $globDir .'company/dwarves/thorin.txt',
            $globDir .'company/wizards/gandalf.txt',
            $globDir .'company/wizards/radagast.txt',
            $globDir .'lipsum/dolor/amet/adipiscit.txt',
            $globDir .'lipsum/dolor/amet/elit.txt',
            $globDir .'lipsum/dolor/amet/lorem.txt',
            $globDir .'lipsum/dolor/dolor.txt',
            $globDir .'lipsum/dolor/valuptatos.js',
            $globDir .'lipsum/root.js',
            $globDir .'newdir/dummy/dummy.js',
            $globDir .'newdir/dummy/leaf.txt',
            $globDir .'newdir/file.txt'
        ), FilesystemUtils::glob($globDir .'**/*.*'), 'Failed to assert using a simple double star wildcard.');

        $this->assertEquals(array(
            $globDir .'company/bilbo.txt',
            $globDir .'company/dwarves/bifur.txt',
            $globDir .'company/dwarves/bombur.txt',
            $globDir .'company/dwarves/oin.txt',
            $globDir .'company/dwarves/thorin.txt',
            $globDir .'company/wizards/gandalf.txt',
            $globDir .'company/wizards/radagast.txt',
            $globDir .'lipsum/dolor/amet/adipiscit.txt',
            $globDir .'lipsum/dolor/amet/elit.txt',
            $globDir .'lipsum/dolor/amet/lorem.txt',
            $globDir .'lipsum/dolor/dolor.txt',
            $globDir .'lipsum/dolor/valuptatos.js',
            $globDir .'lipsum/root.js',
            $globDir .'newdir/dummy/dummy.js',
            $globDir .'newdir/dummy/leaf.txt',
            $globDir .'newdir/file.txt'
        ), FilesystemUtils::glob($globDir .'**/*.*', GLOB_BRACE), 'Failed to assert using a simple double star wildcard with GLOB_BRACE flag.');

        $this->assertEquals(array(
            $globDir .'app.js',
            $globDir .'app.min.js',
            $globDir .'company/bilbo.txt',
            $globDir .'company/dwarves/bifur.txt',
            $globDir .'company/dwarves/bombur.txt',
            $globDir .'company/dwarves/oin.txt',
            $globDir .'company/dwarves/thorin.txt',
            $globDir .'company/wizards/gandalf.txt',
            $globDir .'company/wizards/radagast.txt',
            $globDir .'dolor.txt',
            $globDir .'global.js',
            $globDir .'global.min.js',
            $globDir .'ipsum.txt',
            $globDir .'lipsum/dolor/amet/adipiscit.txt',
            $globDir .'lipsum/dolor/amet/elit.txt',
            $globDir .'lipsum/dolor/amet/lorem.txt',
            $globDir .'lipsum/dolor/dolor.txt',
            $globDir .'lipsum/dolor/valuptatos.js',
            $globDir .'lipsum/root.js',
            $globDir .'lorem.txt',
            $globDir .'newdir/dummy/dummy.js',
            $globDir .'newdir/dummy/leaf.txt',
            $globDir .'newdir/file.txt',
        ), FilesystemUtils::glob($globDir .'{,**}/*.*', GLOB_NOSORT | GLOB_BRACE), 'Failed to assert using double star wildcard inside braces.');

        $this->assertEquals(array(
            $globDir .'app.js',
            $globDir .'app.min.js',
            $globDir .'company/bilbo.txt',
            $globDir .'company/dwarves/bifur.txt',
            $globDir .'company/dwarves/bombur.txt',
            $globDir .'company/dwarves/oin.txt',
            $globDir .'company/dwarves/thorin.txt',
            $globDir .'company/wizards/gandalf.txt',
            $globDir .'company/wizards/radagast.txt',
            $globDir .'dolor.txt',
            $globDir .'global.js',
            $globDir .'global.min.js',
            $globDir .'ipsum.txt',
            $globDir .'lipsum/dolor/amet/adipiscit.txt',
            $globDir .'lipsum/dolor/amet/elit.txt',
            $globDir .'lipsum/dolor/amet/lorem.txt',
            $globDir .'lipsum/dolor/dolor.txt',
            $globDir .'lipsum/dolor/valuptatos.js',
            $globDir .'lipsum/root.js',
            $globDir .'lorem.txt',
            $globDir .'newdir/dummy/dummy.js',
            $globDir .'newdir/dummy/leaf.txt',
            $globDir .'newdir/file.txt',
        ), FilesystemUtils::glob($globDir .'{,**/}*.*', GLOB_NOSORT | GLOB_BRACE), 'Failed to assert using double star wildcard inside braces, different style.');
    }

}
