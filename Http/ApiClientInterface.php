<?php

declare(strict_types=1);

namespace ML\DeveloperTest\Http;

use Psr\Http\Message\ResponseInterface;

interface ApiClientInterface
{
    /**
     * Fire request for getting geolocation data
     *
     * @param array $body
     *
     * @return ResponseInterface|null
     */
    public function get(array $body = []): ?ResponseInterface;

}
