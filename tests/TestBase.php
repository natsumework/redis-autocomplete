<?php

namespace Natsumework\RedisAutocomplete\Tests;

use Natsumework\RedisAutocomplete\Autocomplete;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestBase extends BaseTestCase
{
    /**
     * @var Autocomplete
     */
    protected $autocomplete;

    protected $connection = 'default';

    protected $prefix = 'redis-autocomplete';

    protected $ttl = 60 * 60 * 24;

    protected function setUp(): void
    {
        parent::setUp();

        $this->autocomplete = new Autocomplete($this->getConfig());
    }

    protected function getConfig()
    {
        return [
            'connection' => $this->connection,
            'prefix' => $this->prefix,
            'ttl' => $this->ttl,
        ];
    }
}
