<?php

declare(strict_types=1);

namespace ML\DeveloperTest\Http\Request;

interface BuilderInterface
{
    /**
     * Add fields to fetch from api
     *
     * @param array $fields
     * @return void
     */
    public function addFields(array $fields): void;

    /**
     * Add api access key to request
     *
     * @param string $key
     * @return void
     */
    public function addAccessKey(string $key): void;

    /**
     * Add ip to check key to request
     *
     * @param string $ip
     * @return void
     */
    public function addIp(string $ip): void;

    /**
     * Add timeout to request
     *
     * @param string $timeOut
     * @return void
     */
    public function addTimeOut(string $timeOut): void;

    /**
     * Add connect_timeout to request
     *
     * @param string $connectTimeOut
     * @return void
     */
    public function addConnectTimeOut(string $connectTimeOut): void;

    /**
     * Add ip to check key to request
     *
     * @param string $url
     * @return void
     */
    public function addUrl(string $url): void;

    /**
     * Returning request data
     *
     * @return array
     */
    public function getRequestData(): array;
}
