<?php

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2013, TiMESPLiNTER Webdevelopment
 * @version 1.0.0
 */

namespace test\StringUtils;

use timesplinter\tsfw\common\StringUtils;

class StringUtilsTest extends \PHPUnit_Framework_TestCase {

	public function testAfterFirst() {
		$this->assertSame(StringUtils::afterFirst('foo/bar', '/'), 'bar', 'Test 1');
		$this->assertSame(StringUtils::afterFirst('foo/', '/'), '', 'Test 2');
	}

	public function testBeforeFirst() {
		$this->assertSame(StringUtils::beforeFirst('foo/bar', '/'), 'foo', 'Test 1');
		$this->assertSame(StringUtils::beforeFirst('/bar', '/'), '', 'Test 2');
	}

	public function testBetween() {
		$this->assertSame(StringUtils::between('foobarbaz', 'foo', 'baz'), 'bar', 'Test 1');
		$this->assertSame(StringUtils::between('foobarbaz', 'fooba', ''), '', 'Test 2');
		$this->assertSame(StringUtils::between('foobarbaz', 'foobar', 'baz'), '', 'Test 3');
	}

	public function testInsertBeforeLast() {
		$this->assertSame(StringUtils::insertBeforeLast('foo bar', 'bar', 'test '), 'foo test bar', 'Test 1');
	}

    public function testStartsWith() {
        $this->assertEquals(StringUtils::startsWith('foobar', 'f'), true, 'Test 1');
        $this->assertEquals(StringUtils::startsWith('foobar', 'o'), false, 'Test 2');
        $this->assertEquals(StringUtils::startsWith('foobar', 'fo'), true, 'Test 3');
        $this->assertEquals(StringUtils::startsWith('foobar', 'foobar'), true, 'Test 4');
    }

    public function testEndsWith() {
        $this->assertEquals(StringUtils::endsWith('foobar', 'f'), false, 'Test 1');
        $this->assertEquals(StringUtils::endsWith('foobar', 'a'), false, 'Test 2');
        $this->assertEquals(StringUtils::endsWith('foobar', 'ar'), true, 'Test 3');
        $this->assertEquals(StringUtils::endsWith('foobar', 'foobar'), true, 'Test 4');
    }
}

/* EOF */