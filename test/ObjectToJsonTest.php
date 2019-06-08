<?php
/**
 * Created by PhpStorm.
 * Date: 03.06.2019
 * Time: 16:49
 */

use effect\mapper\JsonMapper;
use PHPUnit\Framework\TestCase;

class ObjectToJsonTest extends TestCase
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
    public function testStringToJson()
    {
        self::assertThat($this->mapper->stringify("Hallo Welt!"), self::equalTo('"Hallo Welt!"'));
    }

    /**
     * UnitTest test method
     *
     * @throws Exception
     */
    public function testIntToJson()
    {
        self::assertThat($this->mapper->stringify(42), self::equalTo('42'));
    }

    /**
     * UnitTest test method
     *
     * @throws Exception
     */
    public function testSimpleClassWithOneString()
    {
        self::assertThat($this->mapper->stringify(new SimpleTestClass('MyCustomMessage')), self::equalTo('{"message":"MyCustomMessage"}'));
    }

    /**
     * UnitTest test method
     *
     * @throws Exception
     */
    public function testWithNestedClass()
    {
        self::assertThat(
            $this->mapper->stringify(new NestedTestClass('MyCustomMessage', new NestedTestClass('AnyOtherMessage'))),
            self::equalTo(
                '{'.
                    '"message":"MyCustomMessage",'.
                    '"nested":{'.
                        '"message":"AnyOtherMessage",'.
                        '"nested":null'.
                    '}'.
                '}'
            ));
    }

    /**
     * UnitTest test method
     *
     * @throws Exception
     */
    public function testDateTimeReturnedAsISO8601()
    {
        self::assertThat($this->mapper->stringify(new DateTime('2011-01-01T15:03:01.012345Z')), self::equalTo('"2011-01-01T15:03:01+0000"'));
    }

    /**
     * UnitTest test method
     *
     * @throws Exception
     */
    public function testFieldWithJsonIgnoreAnnotationIsNotPresentInJSON()
    {
        self::assertThat($this->mapper->stringify(new JsonIgnoreTestClass("someString")), self::equalTo('{"message":"someString"}'));
    }
}

class SimpleTestClass {
    private $message;

    public function __construct(string $message) {
        $this->message = $message;
    }
}

class NestedTestClass {
    private $message;
    private $nested;

    public function __construct(string $message, NestedTestClass $nested = null) {
        $this->message = $message;
        $this->nested = $nested;
    }
}

class JsonIgnoreTestClass {
    private $message;
    /**
     * @var string
     * @JsonIgnore
     */
    private $copiedMessage;

    public function __construct(string $message) {
        $this->message = $message;
        $this->copiedMessage = $message;
    }
}
