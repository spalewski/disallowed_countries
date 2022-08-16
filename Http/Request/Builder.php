<?php

declare(strict_types=1);

namespace ML\DeveloperTest\Http\Request;

class Builder implements BuilderInterface
{
    /**
     * @var array
     */
    private array $data = [];

    public function __construct()
    {
    }

    public function addFields(array $fields = null): void
    {
        $fieldsString = implode(',', $fields);

        $this->data['params']['query']['fields'] = $fieldsString;
    }

    public function addAccessKey(string $key = null): void
    {
        $this->data['params']['query']['access_key'] = $key;
    }

    public function addTimeOut(string $timeout = null): void
    {
        $this->data['params']['timeout'] = $timeout;
    }

    public function addConnectTimeOut(string $connectTimeOut): void
    {
        $this->data['params']['connect_timeout'] = $connectTimeOut;
    }

    public function addIp(string $ip = null): void
    {
        $this->data['ip'] = $ip;
    }

    public function addUrl(string $url): void
    {
        $this->data['url'] = [
            'config' => [
                'base_uri' => $url
            ]
        ];
    }

    public function getRequestData(): array
    {
        return $this->data;
    }
}
