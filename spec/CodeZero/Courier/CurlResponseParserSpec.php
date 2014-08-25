<?php namespace spec\CodeZero\Courier;

use CodeZero\Courier\ResponseCodes;
use CodeZero\Curl\Response as CurlResponse;
use CodeZero\Curl\ResponseInfo as CurlResponseInfo;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CurlResponseParserSpec extends ObjectBehavior {

    function let(ResponseCodes $responseCodes)
    {
        $this->beConstructedWith($responseCodes);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CodeZero\Courier\CurlResponseParser');
    }

    function it_returns_a_courier_response(CurlResponse $curlResponse, CurlResponseInfo $curlResponseInfo, ResponseCodes $responseCodes)
    {
        $curlResponse->getBody()->willReturn('blabla');

        $curlResponse->info()->willReturn($curlResponseInfo);
        $curlResponseInfo->getContentType()->willReturn('Content-Type: text/plain; Charset=UTF-8');
        $curlResponseInfo->getHttpCode()->willReturn(200);

        $responseCodes->getMessage(200)->willReturn('ok');

        $this->parse($curlResponse)->shouldReturnAnInstanceOf('\CodeZero\Courier\Response');
    }

}