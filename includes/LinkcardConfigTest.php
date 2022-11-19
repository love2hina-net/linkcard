<?php declare(strict_types=1);
namespace love2hina\wordpress\linkcard;

// TODO:
const LINKCARD_VERSION = '1.0.0';
const LINKCARD_UID = 'love2hina-linkcard';
const LINKCARD_PREFIX = 'love2hina_linkcard_';

require_once(dirname(__FILE__) . '/WordPressMock.php');
require_once(dirname(__FILE__) . '/LinkcardConfig.php');

final class LinkcardConfigTest extends \PHPUnit\Framework\TestCase
{

    public function testKeysEmpty()
    {
        $config = new LinkcardConfig();

        $this->assertSame(array(), $config->keys());
    }

    public function testKeysAny()
    {
        $config = new LinkcardConfig();
        $config->test = '';

        $this->assertSame(array('test'), $config->keys());
    }

    public function testIssetEmpty()
    {
        $config = new LinkcardConfig();

        $this->assertSame(false, isset($config->test));
    }

    public function testIssetAny()
    {
        $config = new LinkcardConfig();
        $config->test = '';

        $this->assertSame(true, isset($config->test));
    }

    public function testUnset()
    {
        $config = new LinkcardConfig();
        $config->test = '';

        $this->assertSame(true, isset($config->test));
        unset($config->test);
        $this->assertSame(false, isset($config->test));
    }

    public function testGetEmpty()
    {
        $config = new LinkcardConfig();

        $this->assertNull($config->test);
    }

    public function testGetSetAny()
    {
        $config = new LinkcardConfig();
        $config->test = 'sample';

        $this->assertSame('sample', $config->test);
    }

}
