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

    /**
     * UnitTest test method
     *
     * @throws Exception
     */
    public function testAA()
    {
        $map = $this->mapper->map('["string1","string2"]');
        self::assertIsArray($map);
        self::assertThat($map, self::equalTo(["string1", "string2"]));
    }

    /**
     * UnitTest test method
     *
     * @throws Exception
     */
    public function testSimpleString2()
    {
        $map = $this->mapper->map('"string"');
        self::assertThat($map, self::equalTo("string"));
    }

    /**
     * UnitTest test method
     *
     * @throws Exception
     */
    public function testThrowEx()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->mapper->map('{}', 'NotExistingClassName');
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
    public function testArrayOfStrings()
    {
        $map = $this->mapper->map('[{"message":"Hello World!"}]', SimpleTestObject::class);
        self::assertIsArray($map);
        self::assertThat(count($map), self::equalTo(1));
        self::assertThat($map[0]->getMessage(), self::equalTo('Hello World!'));
    }

    /**
     * UnitTest test method
     *
     * @throws Exception
     */
    public function testArrayOfObjectsWithTwoObjects()
    {
        $map = $this->mapper->map('[{"message":"Hello World!"},{"message":"another"}]', SimpleTestObject::class);
        self::assertIsArray($map);
        self::assertThat(count($map), self::equalTo(2));
        self::assertThat($map[0]->getMessage(), self::equalTo('Hello World!'));
        self::assertThat($map[1]->getMessage(), self::equalTo('another'));
    }

    /**
     * UnitTest test method
     *
     * @throws Exception
     */
    public function testSimpleStringInJSON()
    {
        $map = $this->mapper->map('"Hello"', SimpleTestObject::class);
        self::assertThat($map, self::equalTo('Hello'));
    }

    /**
     * UnitTest test method
     *
     * @throws Exception
     */
    public function testArrayWithKeyAndValues()
    {
        $map = $this->mapper->map('{"key1":"value1","key2":"value2"}');
        self::assertIsObject($map);
        self::assertThat($map->key1, self::equalTo("value1"));
        self::assertThat($map->key2, self::equalTo("value2"));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = new JsonMapper();
    }
}

class SimpleTestObject
{
    public $message;

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}
