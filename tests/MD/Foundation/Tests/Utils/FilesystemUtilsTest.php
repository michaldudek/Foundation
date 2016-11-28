<?php
namespace MD\Foundation\Tests\Utils;

use MD\Foundation\Utils\FilesystemUtils;

/**
 * @coversDefaultClass \MD\Foundation\Utils\FilesystemUtils
 */
class FilesystemUtilsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::glob()
     */
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

    /**
     * @covers ::glob()
     */
    public function testGlobBrace() {
        $globDir = rtrim(realpath(__DIR__ .'/../TestFixtures/glob/'), '/') .'/';
        $this->assertEquals(array(
            $globDir .'config.ini',
            $globDir .'config.php',
            $globDir .'config.xml',
            $globDir .'config.yml'
        ), FilesystemUtils::glob($globDir .'config.{php,ini,yml,xml}', GLOB_BRACE));
        $this->assertEquals(array(
            $globDir .'lipsum/dolor/amet/adipiscit.txt',
            $globDir .'lipsum/dolor/amet/elit.txt',
            $globDir .'lipsum/dolor/amet/lorem.txt'
        ), FilesystemUtils::glob($globDir .'lipsum/dolor/amet/{adipiscit,elit,lorem}.{txt,log}', GLOB_BRACE));
    }

    /**
     * @covers ::glob()
     */
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
            $globDir .'config.ini',
            $globDir .'config.php',
            $globDir .'config.xml',
            $globDir .'config.yml',
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
        ), FilesystemUtils::glob($globDir .'{,**}/*.*', GLOB_BRACE), 'Failed to assert using double star wildcard inside braces.');

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
            $globDir .'config.ini',
            $globDir .'config.php',
            $globDir .'config.xml',
            $globDir .'config.yml',
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
        ), FilesystemUtils::glob($globDir .'{,**/}*.*', GLOB_BRACE), 'Failed to assert using double star wildcard inside braces, different style.');

        $this->assertEquals(array(
            $globDir .'app.js',
            $globDir .'app.min.js',
            $globDir .'config.ini',
            $globDir .'config.php',
            $globDir .'config.xml',
            $globDir .'config.yml',
            $globDir .'dolor.txt',
            $globDir .'global.js',
            $globDir .'global.min.js',
            $globDir .'ipsum.txt',
            $globDir .'lorem.txt',
            $globDir .'company/bilbo.txt',
            $globDir .'company/dwarves/bifur.txt',
            $globDir .'company/dwarves/bombur.txt',
            $globDir .'company/dwarves/oin.txt',
            $globDir .'company/dwarves/thorin.txt',
            $globDir .'company/wizards/gandalf.txt',
            $globDir .'company/wizards/radagast.txt',
            $globDir .'lipsum/root.js',
            $globDir .'lipsum/dolor/dolor.txt',
            $globDir .'lipsum/dolor/valuptatos.js',
            $globDir .'lipsum/dolor/amet/adipiscit.txt',
            $globDir .'lipsum/dolor/amet/elit.txt',
            $globDir .'lipsum/dolor/amet/lorem.txt',
            $globDir .'newdir/file.txt',
            $globDir .'newdir/dummy/dummy.js',
            $globDir .'newdir/dummy/leaf.txt',
        ), FilesystemUtils::glob($globDir .'{,**/}*.*', FilesystemUtils::GLOB_ROOTFIRST | GLOB_BRACE), 'Failed to sort with root first.');

        $this->assertEquals(array(
            $globDir .'company/dwarves/bifur.txt',
            $globDir .'company/dwarves/bombur.txt',
            $globDir .'company/dwarves/oin.txt',
            $globDir .'company/dwarves/thorin.txt',
            $globDir .'company/wizards/gandalf.txt',
            $globDir .'company/wizards/radagast.txt',
            $globDir .'company/bilbo.txt',
            $globDir .'lipsum/dolor/amet/adipiscit.txt',
            $globDir .'lipsum/dolor/amet/elit.txt',
            $globDir .'lipsum/dolor/amet/lorem.txt',
            $globDir .'lipsum/dolor/dolor.txt',
            $globDir .'lipsum/dolor/valuptatos.js',
            $globDir .'lipsum/root.js',
            $globDir .'newdir/dummy/dummy.js',
            $globDir .'newdir/dummy/leaf.txt',
            $globDir .'newdir/file.txt',
            $globDir .'app.js',
            $globDir .'app.min.js',
            $globDir .'config.ini',
            $globDir .'config.php',
            $globDir .'config.xml',
            $globDir .'config.yml',
            $globDir .'dolor.txt',
            $globDir .'global.js',
            $globDir .'global.min.js',
            $globDir .'ipsum.txt',
            $globDir .'lorem.txt',
        ), FilesystemUtils::glob($globDir .'{,**/}*.*', FilesystemUtils::GLOB_CHILDFIRST | GLOB_BRACE), 'Failed to sort with child first.');
    }

}
