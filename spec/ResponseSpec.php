<?php namespace spec\CodeZero\Courier;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResponseSpec extends ObjectBehavior {

    private static $responseJson = '[{"key":"value"},{"key":"value"}]';
    private static $responseSerializedJson = 's:33:"[{"key":"value"},{"key":"value"}]";'; //=> Serialized JSON string
    private static $responseSerializedArray = 'a:2:{i:0;a:1:{s:3:"key";s:5:"value";}i:1;a:1:{s:3:"key";s:5:"value";}}'; //=> Serialized array
    private static $responseToArray = [["key" => "value"], ["key" => "value"]];

    private static $responseTypeJson = "application/json";
    private static $responseTypeSerialized = "application/binary";
    private static $responseCharset = "utf-8";
    private static $httpCode = 200;
    private static $httpMessage = "OK";

    function let()
    {
        $this->beConstructedWith(self::$responseJson, self::$responseTypeJson, self::$responseCharset, self::$httpCode, self::$httpMessage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CodeZero\Courier\Response');
    }

    function it_gets_the_response_body()
    {
        $this->getBody()->shouldReturn(self::$responseJson);
    }

    function it_gets_the_response_type()
    {
        $this->getResponseType()->shouldReturn(self::$responseTypeJson);
    }

    function it_gets_the_response_charset()
    {
        $this->getResponseCharset()->shouldReturn(self::$responseCharset);
    }

    function it_gets_the_http_code()
    {
        $this->getHttpCode()->shouldReturn(self::$httpCode);
    }

    function it_gets_the_http_message()
    {
        $this->getHttpMessage()->shouldReturn(self::$httpMessage);
    }

    function it_converts_a_json_response_to_objects()
    {
        // json_decode($string, false) returns an array
        // of stdClass objects with a property "key": "value"
        $obj = (object) ["key" => "value"];
        $array = [$obj,$obj];

        $this->toObjects()->shouldBeLike($array);
    }

    function it_converts_a_json_response_to_an_array()
    {
        $this->toArray()->shouldReturn(self::$responseToArray);
    }

    function it_converts_a_serialized_array_to_an_array()
    {
        $this->beConstructedWith(self::$responseSerializedArray, self::$responseTypeSerialized, self::$responseCharset, self::$httpCode, self::$httpMessage);
        $this->toArray()->shouldReturn(self::$responseToArray);
    }

    function it_converts_serialized_json_to_an_array()
    {
        $this->beConstructedWith(self::$responseSerializedJson, self::$responseTypeSerialized, self::$responseCharset, self::$httpCode, self::$httpMessage);
        $this->toArray()->shouldReturn(self::$responseToArray);
    }

    function it_throws_when_converting_a_serialized_response_to_an_array_fails()
    {
        $this->beConstructedWith('invalid data', self::$responseTypeSerialized, self::$responseCharset, self::$httpCode, self::$httpMessage);
        $this->shouldThrow('CodeZero\Courier\Exceptions\ResponseConversionException')->duringToArray();
    }

    function it_throws_when_converting_a_json_response_to_an_array_fails()
    {
        $this->beConstructedWith('invalid data', self::$responseTypeJson, self::$responseCharset, self::$httpCode, self::$httpMessage);
        $this->shouldThrow('CodeZero\Courier\Exceptions\ResponseConversionException')->duringToArray();
    }

    function it_throws_when_converting_a_json_response_to_objects_fails()
    {
        $this->beConstructedWith('invalid data', self::$responseTypeJson, self::$responseCharset, self::$httpCode, self::$httpMessage);
        $this->shouldThrow('CodeZero\Courier\Exceptions\ResponseConversionException')->duringToObjects();
    }

    function it_throws_when_the_response_type_is_invalid()
    {
        $this->beConstructedWith(self::$responseJson, 'text/plain', self::$responseCharset, self::$httpCode, self::$httpMessage);
        $this->shouldThrow('CodeZero\Courier\Exceptions\ResponseConversionException')->duringToArray();
        $this->shouldThrow('CodeZero\Courier\Exceptions\ResponseConversionException')->duringToObjects();
    }

}

