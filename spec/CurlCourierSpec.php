<?php namespace spec\CodeZero\Courier;

use CodeZero\Courier\Cache\Cache;
use CodeZero\Courier\CurlResponseParser;
use CodeZero\Courier\Response;
use CodeZero\Curl\Request as CurlRequest;
use CodeZero\Curl\Response as CurlResponse;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CurlCourierSpec extends ObjectBehavior {

    function let(CurlRequest $curl, CurlResponseParser $responseParser, Cache $cache)
    {
        $this->beConstructedWith($curl, $responseParser, $cache);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CodeZero\Courier\CurlCourier');
    }

    function it_sends_a_get_request_and_returns_a_response_on_success(CurlRequest $curl, CurlResponseParser $responseParser, CurlResponse $curlResponse, Response $response)
    {
        $url = 'http://my.site/api';
        $data = ['do' => 'something', 'with' => 'this'];
        $headers = ['Some Header' => 'Some Value'];

        $curl->get($url, $data, $headers)->shouldBeCalled()->willReturn($curlResponse);
        $responseParser->parse($curlResponse)->shouldBeCalled()->willReturn($response);
        $response->getHttpCode()->shouldBeCalled()->willReturn(200);

        $this->get($url, $data, $headers)->shouldReturn($response);
    }

    function it_sends_a_post_request_and_returns_a_response_on_success(CurlRequest $curl, CurlResponseParser $responseParser, CurlResponse $curlResponse, Response $response)
    {
        $url = 'http://my.site/api';
        $data = ['do' => 'something', 'with' => 'this'];
        $headers = ['Some Header' => 'Some Value'];

        $curl->post($url, $data, $headers)->shouldBeCalled()->willReturn($curlResponse);
        $responseParser->parse($curlResponse)->shouldBeCalled()->willReturn($response);
        $response->getHttpCode()->shouldBeCalled()->willReturn(200);

        $this->post($url, $data, $headers)->shouldReturn($response);
    }

    function it_sends_a_put_request_and_returns_a_response_on_success(CurlRequest $curl, CurlResponseParser $responseParser, CurlResponse $curlResponse, Response $response)
    {
        $url = 'http://my.site/api';
        $data = ['do' => 'something', 'with' => 'this'];
        $headers = ['Some Header' => 'Some Value'];

        $curl->put($url, $data, $headers)->shouldBeCalled()->willReturn($curlResponse);
        $responseParser->parse($curlResponse)->shouldBeCalled()->willReturn($response);
        $response->getHttpCode()->shouldBeCalled()->willReturn(200);

        $this->put($url, $data, $headers)->shouldReturn($response);
    }

    function it_sends_a_patch_request_and_returns_a_response_on_success(CurlRequest $curl, CurlResponseParser $responseParser, CurlResponse $curlResponse, Response $response)
    {
        $url = 'http://my.site/api';
        $data = ['do' => 'something', 'with' => 'this'];
        $headers = ['Some Header' => 'Some Value'];

        $curl->patch($url, $data, $headers)->shouldBeCalled()->willReturn($curlResponse);
        $responseParser->parse($curlResponse)->shouldBeCalled()->willReturn($response);
        $response->getHttpCode()->shouldBeCalled()->willReturn(200);

        $this->patch($url, $data, $headers)->shouldReturn($response);
    }

    function it_sends_a_delete_request_and_returns_a_response_on_success(CurlRequest $curl, CurlResponseParser $responseParser, CurlResponse $curlResponse, Response $response)
    {
        $url = 'http://my.site/api';
        $data = ['do' => 'something', 'with' => 'this'];
        $headers = ['Some Header' => 'Some Value'];

        $curl->delete($url, $data, $headers)->shouldBeCalled()->willReturn($curlResponse);
        $responseParser->parse($curlResponse)->shouldBeCalled()->willReturn($response);
        $response->getHttpCode()->shouldBeCalled()->willReturn(200);

        $this->delete($url, $data, $headers)->shouldReturn($response);
    }

    function it_throws_on_http_error(CurlRequest $curl, CurlResponseParser $responseParser, CurlResponse $curlResponse, Response $response)
    {
        $url = 'http://my.site/api';

        $curl->get($url, [], [])->shouldBeCalled()->willReturn($curlResponse);
        $responseParser->parse($curlResponse)->shouldBeCalled()->willReturn($response);
        $response->getHttpCode()->shouldBeCalled()->willReturn(404);
        $response->getHttpMessage(404)->shouldBeCalled()->willReturn('error message');

        $this->shouldThrow('CodeZero\Courier\Exceptions\HttpRequestException')->duringGet($url);
    }

    function it_throws_on_curl_error(CurlRequest $curl)
    {
        $url = 'http://my.site/api';
        $curl->get($url, [], [])->willThrow('CodeZero\Curl\RequestException');
        $this->shouldThrow('CodeZero\Courier\Exceptions\RequestException')->duringGet($url);
    }

    function it_sets_basic_authentication(CurlRequest $curl)
    {
        $curl->setBasicAuthentication('johndoe', 'secret')->shouldBeCalled();
        $this->setBasicAuthentication('johndoe', 'secret');
    }

    function it_unsets_basic_authentication(CurlRequest $curl)
    {
        $curl->unsetBasicAuthentication()->shouldBeCalled();
        $this->unsetBasicAuthentication();
    }

    function it_forgets_cached_responses(Cache $cache)
    {
        $cache->forget()->shouldBeCalled();
        $this->forgetCache();
    }

    function it_stores_a_response_in_cache_if_caching_is_enabled(CurlRequest $curl, CurlResponseParser $responseParser, Cache $cache, CurlResponse $curlResponse, Response $response)
    {
        $url = 'http://my.site/api';
        $cacheMinutes = 30;

        $cache->findResponse('get', $url, [], [], '')->willReturn(false);
        $curl->get($url, [], [])->willReturn($curlResponse);
        $responseParser->parse($curlResponse)->willReturn($response);
        $response->getHttpCode()->willReturn(200);
        $cache->storeResponse($response, 'get', $url, [], [], '', $cacheMinutes)->shouldBeCalled();
        $this->get($url, [], [], $cacheMinutes);
    }

    function it_returns_a_cached_response_if_caching_is_enabled(CurlRequest $curl, CurlResponseParser $responseParser, Cache $cache, CurlResponse $curlResponse, Response $response)
    {
        $url = 'http://my.site/api';
        $cacheMinutes = 30;

        $cache->findResponse('get', $url, [], [], '')->willReturn($response);
        $curl->get($url, [], [])->shouldNotBeCalled();
        $responseParser->parse($curlResponse)->shouldNotBeCalled();
        $response->getHttpCode()->shouldNotBeCalled();
        $cache->storeResponse($response, 'get', $url, [], [], $cacheMinutes)->shouldNotBeCalled();
        $this->get($url, [], [], $cacheMinutes);
    }

    function it_does_not_store_the_response_in_cache_if_minutes_is_set_to_zero(CurlRequest $curl, CurlResponseParser $responseParser, Cache $cache, CurlResponse $curlResponse, Response $response)
    {
        $url = 'http://my.site/api';
        $cacheMinutes = 0;

        $cache->findResponse('get', $url, [], [], '')->willReturn(false);
        $curl->get($url, [], [])->willReturn($curlResponse);
        $responseParser->parse($curlResponse)->willReturn($response);
        $response->getHttpCode()->willReturn(200);
        $cache->storeResponse($response, 'get', $url, [], [], $cacheMinutes)->shouldNotBeCalled();
        $this->get($url, [], [], $cacheMinutes);
    }

}