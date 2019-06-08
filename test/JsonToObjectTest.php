<?php
/**
 * Created by PhpStorm.
 * Date: 03.06.2019
 * Time: 19:03
 */

use effect\mapper\JsonMapper;
use PHPUnit\Framework\TestCase;

class JsonToObjectTest extends TestCase
{
    /**
     * @var JsonMapper
     */
    private $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = new JsonMapper();
    }

    /**
     * UnitTest test method
     *
     * @throws Exception
     */
    public function testSimpleString()
    {
        $map = $this->mapper->map('{"message": "Hello World!"}', SimpleTestObject::class);
        self::assertInstanceOf(SimpleTestObject::class, $map);
        self::assertThat($map->getMessage(), self::equalTo('Hello World!'));
    }

    /**
     * UnitTest test method
     *
     * @throws Exception
     */
    public function testSimpleString2()
    {
        $map = $this->mapper->map('{"message123": "Hello World!"}', SimpleTestObject::class);
        self::assertInstanceOf(SimpleTestObject::class, $map);
        self::assertThat($map->getMessage(), self::equalTo('Hello World!'));
    }

    /**
     * UnitTest test method
     *
     * @throws Exception
     */
    public function testArrayOfStrings()
    {
        $map = $this->mapper->mapToArray('["string1","string2"]');
        self::assertInstanceOf(SimpleTestObject::class, $map);
        self::assertThat($map->getMessage(), self::equalTo('Hello World!'));
    }
}

class SimpleTestObject {
    private $message;

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}
