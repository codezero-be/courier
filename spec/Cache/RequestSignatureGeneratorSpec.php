<?php namespace spec\CodeZero\Courier\Cache;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RequestSignatureGeneratorSpec extends ObjectBehavior {

    function it_is_initializable()
    {
        $this->shouldHaveType('CodeZero\Courier\Cache\RequestSignatureGenerator');
    }

    function it_generates_a_request_signature()
    {
        $method = 'get';
        $url = 'http://my.site/api';
        $data = ['do' => 'something', 'with' => 'this'];
        $headers = ['Some Header' => 'Some Value'];
        $basicAuthCredentials = 'username:password';
        $expectedResult = 'get|http://my.site/api|do=something&with=this|Some+Header=Some+Value|username:password';

        $this->generate($method, $url, $data, $headers, $basicAuthCredentials)->shouldReturn($expectedResult);
    }

}