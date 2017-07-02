<?php declare(strict_types=1);

namespace ApiClients\Middleware\Skeleton;

use ApiClients\Foundation\Middleware\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Promise\CancellablePromiseInterface;
use Throwable;
use function React\Promise\reject;
use function React\Promise\resolve;

final class Middleware implements MiddlewareInterface
{
    /**
     * Return the processed $request via a fulfilled promise.
     * When implementing cache or other feature that returns a response, do it with a rejected promise.
     * If neither is possible, e.g. on some kind of failure, resolve the unaltered request.
     *
     * @param  RequestInterface            $request
     * @param  array                       $options
     * @return CancellablePromiseInterface
     */
    public function pre(
        RequestInterface $request,
        string $transactionId,
        array $options = []
    ): CancellablePromiseInterface {
        // TODO: Implement pre() method or add PreTrait and remove this method
        return resolve($request);
    }

    /**
     * Return the processed $response via a promise.
     *
     * @param  ResponseInterface           $response
     * @param  array                       $options
     * @return CancellablePromiseInterface
     */
    public function post(
        ResponseInterface $response,
        string $transactionId,
        array $options = []
    ): CancellablePromiseInterface {
        // TODO: Implement post() method. Or add PostTrait and remove this method
        return resolve($response);
    }

    /**
     * Deal with possible errors that occurred during request/response events.
     *
     * @param  Throwable                   $throwable
     * @param  array                       $options
     * @return CancellablePromiseInterface
     */
    public function error(
        Throwable $throwable,
        string $transactionId,
        array $options = []
    ): CancellablePromiseInterface {
        // TODO: Implement error() method. Or add ErrorTrait and remove this method
        return reject($throwable);
    }
}
