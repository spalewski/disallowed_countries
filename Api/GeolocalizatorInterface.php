<?php

declare(strict_types=1);

namespace ML\DeveloperTest\Api;

interface GeolocalizatorInterface
{
    /**
     * @return mixed|null
     */
    public function getClientGeolocationData();
}
