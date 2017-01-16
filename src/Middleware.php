<?php declare(strict_types=1);

namespace ApiClients\Middleware\Skeleton;

use ApiClients\Foundation\Middleware\DefaultPriorityTrait;
use ApiClients\Foundation\Middleware\MiddlewareInterface;
use ApiClients\Foundation\Middleware\PostTrait;
use ApiClients\Foundation\Middleware\PreTrait;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Promise\CancellablePromiseInterface;
use function React\Promise\resolve;

final class Middleware implements MiddlewareInterface
{
//    use DefaultPriorityTrait;
//    use PreTrait;
//    use PostTrait;

    /**
     * Return the processed $request via a fulfilled promise.
     * When implementing cache or other feature that returns a response, do it with a rejected promise.
     * If neither is possible, e.g. on some kind of failure, resolve the unaltered request.
     *
     * @param RequestInterface $request
     * @param array $options
     * @return CancellablePromiseInterface
     */
    public function pre(RequestInterface $request, array $options = []): CancellablePromiseInterface
    {
        // TODO: Implement pre() method or add PreTrait and remove this method
        return resolve($request);
    }

    /**
     * Return the processed $response via a promise.
     *
     * @param ResponseInterface $response
     * @param array $options
     * @return CancellablePromiseInterface
     */
    public function post(ResponseInterface $response, array $options = []): CancellablePromiseInterface
    {
        // TODO: Implement post() method. Or add PostTrait and remove this method
        return resolve($response);
    }

    /**
     * Priority ranging from 0 to 1000. Where 1000 will be executed first on `pre` and 0 last on `pre`.
     * For `post` the order is reversed.
     *
     * @return int
     */
    public function priority(): int
    {
        // TODO: Implement priority() method or add DefaultPriorityTrait and remove this method
    }
}
