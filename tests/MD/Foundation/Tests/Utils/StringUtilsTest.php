<?php
namespace MD\Foundation\Tests\Utils;

use MD\Foundation\Utils\StringUtils;

use MD\Foundation\Tests\TestFixtures\ItemClass;
use MD\Foundation\Tests\TestFixtures\ItemMagicClass;

/**
 * @coversDefaultClass \MD\Foundation\Utils\StringUtils
 */
class StringUtilsTest extends \PHPUnit_Framework_TestCase
{

    protected $_presets = array(
        'lipsum_sentence' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        'lipsum_word' => 'Lipsum',
        'lipsum_paragraph' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut consequat volutpat risus, vel tincidunt purus fermentum at. Sed vehicula aliquet nibh, at vulputate nunc ullamcorper nec.',
        'lipsum_text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut consequat volutpat risus, vel tincidunt purus fermentum at. Sed vehicula aliquet nibh, at vulputate nunc ullamcorper nec. Vivamus egestas, sapien ac mattis varius, neque turpis luctus justo, eget euismod quam massa et sem. In sed ornare nunc, sed cursus odio. Maecenas tristique auctor auctor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vivamus scelerisque tortor vitae nisi hendrerit fringilla. Vestibulum sed tortor non leo lobortis suscipit. Phasellus non rhoncus ante.

            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus fringilla tellus est. Nunc eu urna pharetra risus eleifend dignissim ac at augue. Duis ultricies at enim eget tempor. Maecenas volutpat elit sem, at volutpat justo convallis nec. Fusce gravida tellus quis sapien vehicula lacinia. Etiam felis libero, posuere eu malesuada ut, tempor id lectus. Morbi magna turpis, pulvinar sollicitudin dolor sed, semper adipiscing quam. Praesent viverra turpis lobortis viverra pulvinar. In hac habitasse platea dictumst. Donec sollicitudin leo eget nunc semper tristique. Praesent in mauris cursus, consectetur lacus at, accumsan dui. Vivamus sit amet sollicitudin libero. Aenean a nunc non erat tincidunt auctor. Aliquam aliquet nisi ac purus blandit interdum.',
        'empty' => '',
        'char' => 'l',
        'no_spaces' => 'Loremipsumdolorsitametconsecteturadipiscingelit'
    );

    protected function _getPreset($name) {
        return $this->_presets[$name];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTruncateInvalidArgument() {
        StringUtils::truncate(array());
    }

    public function testTruncate() {
        $this->assertTrue(strlen(StringUtils::truncate($this->_getPreset('lipsum_sentence'))) < 72);
        $this->assertEmpty(StringUtils::truncate(''));
        $this->assertTrue(strlen(StringUtils::truncate('a')) === 1);
        $this->assertTrue(strlen(StringUtils::truncate('a', 1)) === 1);

        foreach($this->_presets as $str) {
            $this->assertTrue(strlen(StringUtils::truncate($str, 30, '...')) <= 30);
        }

        $this->assertEquals('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', StringUtils::truncate($this->_getPreset('lipsum_sentence')));
        $this->assertEquals('Lorem...', StringUtils::truncate($this->_getPreset('lipsum_sentence'), 10));
        $this->assertEquals('Lorem-', StringUtils::truncate($this->_getPreset('lipsum_sentence'), 10, '-'));
        $this->assertEquals('Lorem', StringUtils::truncate($this->_getPreset('lipsum_sentence'), 10, ''));
        $this->assertEquals('...', StringUtils::truncate($this->_getPreset('lipsum_sentence'), 3));
        $this->assertEquals('..', StringUtils::truncate($this->_getPreset('lipsum_sentence'), 2));
        $this->assertEquals('Lo...', StringUtils::truncate($this->_getPreset('lipsum_sentence'), 5));
    }

    public function testZeroFill() {
        $this->assertEquals('0000', StringUtils::zeroFill(''));
        $this->assertEquals('0001', StringUtils::zeroFill('1'));
        $this->assertEquals('0010', StringUtils::zeroFill(10));
        $this->assertEquals('123123', StringUtils::zeroFill('123123'));
        $this->assertEquals('00ab', StringUtils::zeroFill('ab'));
        $this->assertEquals('000ab', StringUtils::zeroFill('ab', 5));
        $this->assertEquals('000000123456', StringUtils::zeroFill('123456', 12));
    }

    public function testClear() {
        $this->assertEquals('lorem ipsum', StringUtils::clear('   lorem <i>ipsum</i>   '));
        $this->assertEquals('lorem ipsum dolor sit amet', StringUtils::clear('   lorem
            ipsum
    dolor <i>sit</i>,     (amet)'));
        $this->assertEquals('lorem ipsum dolor sit amet adipiscing elit', StringUtils::clear('  lorem `ipsum~ dolor !!! @sit #$ % ^ * (am/et) - _adipiscing+ = [ { } ] | \\ : ; " \' >< elit.???'));
        $this->assertEquals('lorem, ipsum/dolor sit amet', StringUtils::clear('lorem, ipsum/dolor sit amet    ', false));
    }

    public function testGetWords() {
        $this->assertEquals(array('lorem', 'ipsum', 'dolor', 'sit', 'amet'), StringUtils::getWords('lorem ipsum dolor sit amet'));
        $this->assertEmpty(StringUtils::getWords(''));
        $this->assertEquals(array('lorem,', 'ipsum/dolor'), StringUtils::getWords('lorem, <br /> ipsum/dolor    ', false));
    }

    public function testGetFirstWord() {
        $this->assertEquals(null, StringUtils::getFirstWord(''));
        $this->assertEquals('lorem', StringUtils::getFirstWord('    lorem  ipsum dolor / sit amet'));
        $this->assertEquals('lorem', StringUtils::getFirstWord('lor/em ipsum'));
    }

    /**
     * @dataProvider provideStringsWithSentences
     */
    public function testGetFirstSentence($str, $sentence) {
        $this->assertEquals($sentence, StringUtils::getFirstSentence($str));
    }

    public function provideStringsWithSentences() {
        return array(
            array(
                'To be or not to be - that is a question.',
                'To be or not to be - that is a question.'
            ),
            array(
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum lobortis erat id diam elementum elementum. Curabitur auctor vehicula ante.',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
            ),
            array(
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit! Vestibulum lobortis erat id diam elementum elementum. Curabitur auctor vehicula ante.',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit!'
            ),
            array(
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit
                Vestibulum lobortis erat id diam elementum elementum. Curabitur auctor vehicula ante.',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit'
            ),
            array(
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit
Vestibulum lobortis erat id diam elementum elementum. Curabitur auctor vehicula ante.',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit'
            ),
            array(
                '<p>Lorem ipsum dolor sit amet, <span>consectetur</span> adipiscing elit.</p><br><p>Vestibulum lobortis erat id diam elementum elementum. Curabitur auctor vehicula ante.</p>',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
            )
        );
    }

    public function testWordCount() {
        $this->assertEquals(0, StringUtils::wordCount(''));
        $this->assertEquals(2, StringUtils::wordCount('lorem ipsum'));
        $this->assertEquals(5, StringUtils::wordCount('lor/em, ipsum <br /> dolor sit amet   '));
        $this->assertEquals(3, StringUtils::wordCount('Żażółć żółtą gęśl.'));
    }

    public function testTranslit() {
        $strings = array(
            'lorem ipsum dolor sit amet',
            'zażółć żółtą gęśl',
            'ÄäÖöÜüß€',
            'ÀÂÄÈÉÊËÎÏÔŒÙÛÜŸ',
            'àâäèéêëîïôœùûüÿ',
            'Çç€',
            'ÁáÀàÂâǍǎĂăÃãẢảẠạÄäÅåĀāĄąẤấẦầẪẫẨẩẬậẮắẰằẴẵẲẳẶặǺǻ',
            'ĆćĈĉČčĊċÇç',
            'ĎďĐđÐ',
            'ÉéÈèÊêĚěĔĕẼẽẺẻĖėËëĒēĘęẾếỀềỄễỂểẸẹỆệ',
            'ĞğĜĝĠġĢģ',
            'ĤĥĦħ',
            'ÍíÌìĬĭÎîǏǐÏïĨĩĮįĪīỈỉỊị',
            'Ĵĵ',
            'Ķķ',
            'ĹĺĽľĻļ',
            'ŁłĿŀ',
            'ŃńŇňÑñŅņ',
            'ÓóÒòŎŏÔôỐốỒồỖỗỔổǑǒÖöŐőÕõØøǾǿŌōỎỏƠơỚớỜờỠỡỞởỢợỌọỘộ',
            'ṔṕṖṗ',
            'ŔŕŘřŖŗ',
            'ŚśŜŝŠšŞş',
            'ŤťŢţŦŧ',
            'ÚúÙùŬŭÛûǓǔŮůÜüǗǘǛǜǙǚǕǖŰűŨũŲųŪūỦủƯưỨứỪừỮữỬửỰựỤụ',
            'ẂẃẀẁŴŵẄẅ',
            'ÝýỲỳŶŷŸÿỸỹỶỷỴỵ',
            'ŹźŽžŻż',
            'ß'
        );

        foreach($strings as $string) {
            $translit = StringUtils::translit($string);
            $this->assertEquals(1, preg_match('/([a-zAz0-9\s]+)/', $translit), 'Failed to translit string "'. $string .'" - got "'. $translit .'".');
        }

        $this->assertEquals('Ara', StringUtils::translit('Ära'));
        $this->assertEquals('a', substr(strtolower(StringUtils::translit('Ära')), 0, 1));
    }

    public function testUrlFriendly() {
        $strings = array(
            'lorem ipsum dolor sit amet',
            'zażółć żółtą gęśl',
            '<?php echo $stuff->whatever; ?>',
            '123123123',
            123123123,
            '<html><head /></html>',
            'I want to do something like that & whatever'
        );

        foreach($strings as $string) {
            $this->assertEquals(1, preg_match('/([a-z0-9-_]+)/', StringUtils::urlFriendly($string)));
        }

        foreach($strings as $string) {
            $this->assertEquals(1, preg_match('/([a-zA-Z0-9-_]+)/', StringUtils::urlFriendly($string, false)));
        }
    }

    /**
     * @dataProvider provideFileNameStrings
     * @covers ::fileNameFriendly
     */
    public function testFileNameFriendly($string, $fileName) {
        $this->assertEquals($fileName, StringUtils::fileNameFriendly($string));
    }

    public function provideFileNameStrings() {
        return array(
            array('lorem ipsum dolor sit amet', 'lorem-ipsum-dolor-sit-amet'),
            array('zażółć żółtą gęśl', 'zazolc-zolta-gesl'),
            array('<?php echo $stuff->whatever; ?>', 'php-echo-stuff-whatever'),
            array('123123123', '123123123'),
            array(123123123, '123123123'),
            array('<html><head /></html>', 'html-head-html'),
            array('I want to do something like that & whatever', 'I-want-to-do-something-like-that-and-whatever'),
            array('something / else . js', 'something-else-.-js')
        );
    }

    /**
     * @dataProvider provideFileNamePrefixStrings
     */
    public function testFileNamePrefix($file, $prefix, $result) {
        $this->assertEquals($result, StringUtils::fileNamePrefix($file, $prefix));
    }

    public function provideFileNamePrefixStrings() {
        return array(
            array('/var/www/files/lipsum.txt', 'lorem.', '/var/www/files/lorem.lipsum.txt'),
            array('ipsum.txt', 'lorem.', 'lorem.ipsum.txt'),
            array('path/to/ipsum.txt', 'lorem.', 'path/to/lorem.ipsum.txt'),
            array('', 'lorem.', 'lorem.')
        );
    }

    /**
     * @dataProvider provideFileNameSuffixStrings
     */
    public function testFileNameSuffix($file, $suffix, $result) {
        $this->assertEquals($result, StringUtils::fileNameSuffix($file, $suffix));
    }

    public function provideFileNameSuffixStrings() {
        return array(
            array('/var/www/files/lipsum.txt', '.dolor', '/var/www/files/lipsum.dolor.txt'),
            array('ipsum.txt', '.dolor', 'ipsum.dolor.txt'),
            array('path/to/ipsum.txt', '.dolor', 'path/to/ipsum.dolor.txt'),
            array('', '.dolor', '.dolor'),
            array('ipsum', '.dolor', 'ipsum.dolor')
        );
    }

    public function testToCamelCase() {
        $this->assertEquals('whateverElse', StringUtils::toCamelCase('whatever-else'));
        $this->assertEquals('loremIpsumDolorSitAmet', StringUtils::toCamelCase('lorem-ipsum-dolor-sit-amet'));
        $this->assertEmpty(StringUtils::toCamelCase(''));
        $this->assertEquals('loremIpsumDolor', StringUtils::toCamelCase('lorem_ipsum_dolor', '_'));
        $this->assertEquals('lorem_IpsumDolor', StringUtils::toCamelCase('lorem__ipsum_dolor', '_'));
    }

    public function testToHyphenated() {
        $this->assertEquals('whatever-else', StringUtils::toHyphenated('whateverElse'));
        $this->assertEquals('lorem-ipsum-dolor-sit-amet', StringUtils::toHyphenated('loremIpsumDolorSitAmet'));
        $this->assertEmpty(StringUtils::toHyphenated(''));
        $this->assertEquals('lorem-ipsum-dolor', StringUtils::toHyphenated('loremIpsumDolor'));
    }

    public function toSeparated() {
        $this->assertEquals('whatever-else', StringUtils::toSeparated('whateverElse'));
        $this->assertEquals('whatever_else', StringUtils::toSeparated('whateverElse', '_'));
        $this->assertEquals('whatever^else', StringUtils::toSeparated('whateverElse', '^'));
        $this->assertEquals('lorem ipsum dolor sit amet', StringUtils::toSeparated('loremIpsumDolorSitAmet', ' '));
        $this->assertEmpty(StringUtils::toSeparated(''));
        $this->assertEquals('loremipsumdolorsitamet', StringUtils::toSeparated('loremIpsumDolorSitAmet', ''));
    }

    public function testIsEmail() {
        $this->assertTrue(StringUtils::isEmail('michal@michaldudek.pl'));
        $this->assertTrue(StringUtils::isEmail('michal.test@michaldudek.pl'));
        $this->assertTrue(StringUtils::isEmail('michal.test-with-some.thing@123.lipsum.dev.bbc.co.uk'));
        $this->assertTrue(StringUtils::isEmail('michal+appendix@michaldudek.pl'));
        $this->assertTrue(StringUtils::isEmail('michal+appendix1+appendix2@michaldudek.pl'));
        $this->assertFalse(StringUtils::isEmail('----this-is-not-an-(email)(most)+(definitely)@email.com'));
        $this->assertFalse(StringUtils::isEmail('non-email-string'));
        $this->assertFalse(StringUtils::isEmail('pretending@tobeemail'));
        $this->assertFalse(StringUtils::isEmail('@undefined'));
        $this->assertFalse(StringUtils::isEmail(''));
    }

    public function testIsUrl() {
        $this->assertTrue(StringUtils::isUrl('http://www.michaldudek.pl'));
        $this->assertTrue(StringUtils::isUrl('https://michaldudek.pl/some/thing/wicked/this/way/comes.html'));
        $this->assertTrue(StringUtils::isUrl('http://www.michaldudek.pl/?show=news&id=1'));
        $this->assertTrue(StringUtils::isUrl('http://www.michaldudek.pl/?show=news&amp;id=123&title=something+wicked+this+way+comes&categories[]=1&categories[]=2&categories[]=3&tags[main][0]=&tags[main][1]=&something=%20%20%20'));
        $this->assertTrue(StringUtils::isUrl('https://localhost/whatever.html'));
        $this->assertTrue(StringUtils::isUrl('http://127.0.0.1/haha.html'));
        $this->assertTrue(StringUtils::isUrl('http://just-pretending'));
        $this->assertFalse(StringUtils::isUrl(''));
        $this->assertFalse(StringUtils::isUrl('just.a.string'));
    }

    public function testIsClassName() {
        $this->assertFalse(StringUtils::isClassName(''));
        $this->assertTrue(StringUtils::isClassName('lipsum'));
        $this->assertTrue(StringUtils::isClassName('lipsumDolorSitAmet'));
        $this->assertTrue(StringUtils::isClassName('lorem_ipsum'));
        $this->assertFalse(StringUtils::isClassName('MD\Foundation'));
        $this->assertFalse(StringUtils::isClassName('MD\\Foundation'));
        $this->assertFalse(StringUtils::isClassName('123Lipsum'));
        $this->assertFalse(StringUtils::isClassName('some string'));
        $this->assertFalse(StringUtils::isClassName('lorem.ipsum'));
        $this->assertTrue(StringUtils::isClassName('MD\Foundation', true));
        $this->assertTrue(StringUtils::isClassName('MD\\Foundation', true));
        $this->assertTrue(StringUtils::isClassName(get_called_class(), true));
        $this->assertTrue(StringUtils::isClassName('MD\\TestMe\\And\\Throw_Me_Away', true)); // https://si0.twimg.com/profile_images/2727748211/c3d0981ae770f926eedf4eda7505b006.jpeg
        $this->assertFalse(StringUtils::isClassName('MD\\123Wrong', true));
    }

    public function testFixUrlProtocol() {
        $this->assertEquals('http://', StringUtils::fixUrlProtocol(''));
        $this->assertEquals('http://whatever.com', StringUtils::fixUrlProtocol('whatever.com'));
        $this->assertEquals('https://whatever.com', StringUtils::fixUrlProtocol('https://whatever.com'));
        $this->assertEquals('HTTP://WWW.DONTSHOUTATME.COM', StringUtils::fixUrlProtocol('HTTP://WWW.DONTSHOUTATME.COM'));
    }

    public function testRandom() {
        $this->assertEquals(16, strlen(StringUtils::random()));
        $this->assertEquals(8, strlen(StringUtils::random(8)));
        $this->assertEquals(7, strlen(StringUtils::random(7, false)));
        $this->assertEquals(123, strlen(StringUtils::random(123, false, true)));
        $this->assertEquals(90, strlen(StringUtils::random(90, true, true)));
        $this->assertEquals(1, preg_match('/([a-zA-Z0-9]+)/', StringUtils::random()));
        $this->assertEquals(1, preg_match('/([a-z0-9]+)/', StringUtils::random(16, false)));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testParseVariablesInvalidArgument() {
        StringUtils::parseVariables('lipsum', 123);
    }

    public function testParseVariables() {
        $basicResult = 'lorem ipsum dolor sit amet';
        $basicString = 'lorem {var1} dolor {var2}{undefined} amet';

        $this->assertEmpty(StringUtils::parseVariables('', array()));
        $this->assertEquals('lorem ipsum dolor sit amet', StringUtils::parseVariables('lorem {var1} dolor {var2}{undefined} amet', array(
            'var1' => 'ipsum',
            'var2' => 'sit'
        )));

        $variables = new \stdClass();
        $variables->var1 = 'ipsum';
        $variables->var2 = 'sit';
        $this->assertEquals($basicResult, StringUtils::parseVariables($basicString, $variables));

        $variables = new ItemMagicClass(5, 'Lipsum', 1, '2013.07.26');
        $this->assertEquals('Item #5 - "Lipsum", cat: 1 on 2013.07.26', StringUtils::parseVariables('Item #{id} - "{name}", cat: {categoryId} on {date}', $variables));

        $variables = new ItemClass(5, 'Lipsum', 1, '2013.07.26');
        $this->assertEquals('Item #5 - "Lipsum", cat: 1 on 2013.07.26', StringUtils::parseVariables('Item #{id} - "{name}", cat: {categoryId} on {date}', $variables));
    }

    public function testSecondsToTimeString() {
        $this->assertEquals('0:00:00', StringUtils::secondsToTimeString(0));
        $this->assertEquals('00:00', StringUtils::secondsToTimeString(0, true));
        $this->assertEquals('0:00:05', StringUtils::secondsToTimeString(5));
        $this->assertEquals('0:15:36', StringUtils::secondsToTimeString(936));
        $this->assertEquals('15:36', StringUtils::secondsToTimeString(936, true));
        $this->assertEquals('14:54:23', StringUtils::secondsToTimeString(53663));
        $this->assertEquals('-0:00:05', StringUtils::secondsToTimeString(-5));
        $this->assertEquals('-0:15:36', StringUtils::secondsToTimeString(-936));
        $this->assertEquals('-15:36', StringUtils::secondsToTimeString(-936, true));
        $this->assertEquals('-14:54:23', StringUtils::secondsToTimeString(-53663));
    }

    public function testTimeStringToSeconds() {
        $this->assertEquals(0, StringUtils::timeStringToSeconds('0:00:00'));
        $this->assertEquals(0, StringUtils::timeStringToSeconds('00:00'));
        $this->assertEquals(5, StringUtils::timeStringToSeconds('0:00:05'));
        $this->assertEquals(936, StringUtils::timeStringToSeconds('0:15:36'));
        $this->assertEquals(936, StringUtils::timeStringToSeconds('15:36'));
        $this->assertEquals(53663, StringUtils::timeStringToSeconds('14:54:23'));
        $this->assertEquals(-5, StringUtils::timeStringToSeconds('-0:00:05'));
        $this->assertEquals(-936, StringUtils::timeStringToSeconds('-0:15:36'));
        $this->assertEquals(-936, StringUtils::timeStringToSeconds('-15:36'));
        $this->assertEquals(-53663, StringUtils::timeStringToSeconds('-14:54:23'));
    }

    public function testBytesToString() {
        $this->assertEquals('0 b', StringUtils::bytesToString(0));
        $this->assertEquals('1000 b', StringUtils::bytesToString(1000));
        $this->assertEquals('1 kb', StringUtils::bytesToString(1024));
        $this->assertEquals('1 kb', StringUtils::bytesToString(1025));
        $this->assertEquals('500 kb', StringUtils::bytesToString(1024 * 500));
        $this->assertEquals('1.0 MB', StringUtils::bytesToString(1024 * 1024));
        $this->assertEquals('1.5 MB', StringUtils::bytesToString(1024 * 1024 * 1.5));
        $this->assertEquals('1.25 GB', StringUtils::bytesToString(1024 * 1024 * 1024 * 1.25));
    }

    /**
     * @dataProvider provideHexToRgb
     */
    public function testHexToRgb($hex, array $rgb) {
        $this->assertEquals($rgb, StringUtils::hexToRgb($hex));
    }

    public function provideHexToRgb() {
        return array(
            array('#ffffff', array(255, 255, 255)),
            array('ffffff', array(255, 255, 255)),
            array('f00', array(255, 0, 0)),
            array('000000', array(0, 0, 0)),
            array('#000', array(0, 0, 0)),
            array('#C0C0C0', array(192, 192, 192)),
            array('#B8860B', array(184, 134, 11)),
            array('7FFFD4', array(127, 255, 212)),
            array('#ff69B4', array(255, 105, 180))
        );
    }

    /**
     * @dataProvider provideRgbToHex
     */
    public function testRgbToHex($rgb, $hex, $separator = ',') {
        $this->assertEquals($hex, StringUtils::rgbToHex($rgb, $separator));
    }

    public function provideRgbToHex() {
        return array(
            array(array(255,0,255), 'ff00ff'),
            array(array(255, 182, 193), 'ffb6c1'),
            array('119,136,153', '778899'),
            array('75, 0, 130', '4b0082'),
            array('124-252-0', '7cfc00', '-'),
            array('189:183:107', 'bdb76b', ':')
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider provideInvalidRgb
     */
    public function testRgbToHexWithInvalidRgb($invalidRgb) {
        StringUtils::rgbToHex($invalidRgb);
    }

    public function provideInvalidRgb() {
        return array(
            array(array(256, 0, 255)),
            array(array(255, 255, 255, 0.5)),
            array('lorem,ipsum,dolor,sit'),
            array('lorem,ipsum,dolor'),
            array('1lorem,false,true')
        );
    }

    public function testTimeAgo() {
        $this->assertEquals('few seconds ago', StringUtils::timeAgo(time()));
        $this->assertEquals('few seconds ago', StringUtils::timeAgo(time() - 5));
        $this->assertEquals(date('d.m.Y H:i', strtotime('1 month ago')), StringUtils::timeAgo(strtotime('1 month ago')));
        $this->assertNotEquals(date('d.m.Y H:i', strtotime('1 month ago')), StringUtils::timeAgo(strtotime('1 month ago'), 1, '2 months ago'));
        $this->assertEquals(date('d.m.Y', strtotime('1 month ago')), StringUtils::timeAgo(strtotime('1 month ago'), 1, '3 weeks ago', 'd.m.Y'));
        $this->assertEquals('1 minute 5 seconds ago', StringUtils::timeAgo(time() - 65, 2));
        $this->assertEquals('1 minute ago', StringUtils::timeAgo(time() - 65));
        $this->assertEquals('1 hour 13 minutes ago', StringUtils::timeAgo(time() - 73 * 60, 2));
        $this->assertEquals('1 hour 13 minutes 17 seconds ago', StringUtils::timeAgo(time() - 73 * 60 - 17, 3));
        $this->assertEquals('1 hour 13 minutes ago', StringUtils::timeAgo(time() - 73 * 60 - 17, 2));
        $this->assertEquals('1 hour ago', StringUtils::timeAgo(time() - 60 * 60 - 13));
        $this->assertEquals('1 hour ago', StringUtils::timeAgo(time() - 60 * 60));
    }

    public function testStripHtml() {
        $this->assertEquals('lorem ipsum dolor sit amet', StringUtils::stripHtml('lorem <i>ipsum</i> dolor <span>sit amet</span>'));
        $this->assertEquals('lorem ipsum dolor sit amet', StringUtils::stripHtml('lorem <i class="something" data-some-bound-data="123">ipsum</i> dolor sit amet'));
        $this->assertEquals('lorem &gt; ipsum', StringUtils::stripHtml('lorem > ipsum'));
        $this->assertEquals('lorem &lt; ipsum', StringUtils::stripHtml('lorem < ipsum'));
    }

    public function testMultiExplode() {
        $this->assertEquals(array('lorem', 'ipsum', 'dolor', 'sit', 'amet'), StringUtils::multiExplode(' ', 'lorem ipsum_dolor-sit=amet', array('_', '-', '=')));
    }

    public function testSearch() {
        $this->assertTrue(StringUtils::search('lorem ipsum dolor sit amet', 'sit'));
        $this->assertTrue(StringUtils::search('lorem ipsum dolor sit amet', array('undefined', 'ipsum')));
        $this->assertTrue(StringUtils::search('LOREM ipsum DOLOR sit amet', array('undefined', 'ipsum')));
        $this->assertFalse(StringUtils::search('lorem ipsum dolor sit amet', 'what'));
        $this->assertFalse(StringUtils::search('', array()));
        $this->assertFalse(StringUtils::search('lorem ipsum dolor sit amet', array('what', 'happened', 'to', 'us')));
    }

}
