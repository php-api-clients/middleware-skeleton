<?php declare(strict_types=1);

namespace ApiClients\Tests\Middleware\Skeleton;

use ApiClients\Middleware\Skeleton\Middleware;
use ApiClients\Middleware\Skeleton\Options;
use ApiClients\Tools\TestUtilities\TestCase;
use Exception;
use React\EventLoop\Factory;
use RingCentral\Psr7\BufferStream;
use RingCentral\Psr7\Request;
use RingCentral\Psr7\Response;
use function Clue\React\Block\await;

final class MiddlewareTest extends TestCase
{
    public function testPre()
    {
        $request = new Request('GET', 'foo.bar');

        $options = [
            Middleware::class => [
                Options::MY_OPTION => 'Foo',
            ],
        ];

        $middleware = new Middleware();
        $response = await($middleware->pre($request, $options), Factory::create());

        self::assertSame($request, $response);
    }

    public function testPost()
    {
        $request = new Request('GET', 'foo.bar');

        $body = 'foo.bar';
        $stream = new BufferStream(strlen($body));
        $stream->write($body);
        $response = (new Response(200, []))->withBody($stream);

        $options = [
            Middleware::class => [
                Options::MY_OPTION => 'Foo',
            ],
        ];

        $middleware = new Middleware();
        $middleware->pre($request, $options);
        $responseObject = await($middleware->post($response, $options), Factory::create());

        self::assertSame($response->getStatusCode(), $responseObject->getStatusCode());
        self::assertSame($body, $responseObject->getBody()->getContents());
    }

    public function testError()
    {
        self::expectException(Exception::class);
        $middleware = new Middleware();
        await($middleware->error(new Exception(), []), Factory::create());
    }
}
