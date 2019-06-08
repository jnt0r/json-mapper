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
        $map = $this->mapper->map('["message": "Hello World!", "message2": "another"]', SimpleTestObject::class);
//        self::assertIsArray($map);
//        self::assertThat(count($map), self::equalTo(1));
//        self::assertThat($map[0]->getMessage(), self::equalTo('Hello World!'));
    }
}

class SimpleTestObject {
    public $message;

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}
