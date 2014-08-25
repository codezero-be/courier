<?php namespace spec\CodeZero\Courier\Cache;

use CodeZero\Courier\Cache\CacheManager;
use CodeZero\Courier\Cache\RequestSignatureGenerator;
use CodeZero\Courier\Response;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CacheSpec extends ObjectBehavior {

    function let(CacheManager $cacheManager, RequestSignatureGenerator $signature)
    {
        $this->beConstructedWith($cacheManager, $signature);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CodeZero\Courier\Cache\Cache');
    }

    function it_returns_a_response_from_cache(CacheManager $cacheManager, RequestSignatureGenerator $signature)
    {
        $method = 'get';
        $url = 'http://my.site/api';

        $signature->generate($method, $url, [], [])->shouldBeCalled()->willReturn('signature');
        $cacheManager->find('signature')->shouldBeCalled()->willReturn('response');
        $this->findResponse($method, $url)->shouldReturn('response');
    }

    function it_returns_false_if_response_is_not_yet_cached(CacheManager $cacheManager, RequestSignatureGenerator $signature)
    {
        $method = 'get';
        $url = 'http://my.site/api';

        $signature->generate($method, $url, [], [])->shouldBeCalled()->willReturn('signature');
        $cacheManager->find('signature')->shouldBeCalled()->willReturn(false);
        $this->findResponse($method, $url)->shouldReturn(false);
    }

    function it_stores_a_response_in_cache(Response $response, CacheManager $cacheManager, RequestSignatureGenerator $signature)
    {
        $method = 'get';
        $url = 'http://my.site/api';
        $minutes = 30;

        $signature->generate($method, $url, [], [])->shouldBeCalled()->willReturn('signature');
        $cacheManager->store('signature', $response, $minutes)->shouldBeCalled();
        $this->storeResponse($response, $method, $url);
    }

    function it_forgets_all_cached_responses(CacheManager $cacheManager)
    {
        $cacheManager->forget()->shouldBeCalled();
        $this->forget();
    }

}