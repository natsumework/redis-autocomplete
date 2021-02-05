<?php

namespace Natsumework\RedisAutocomplete\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static addPhrases(string $name, $phrases)
 * @method static removePhrase(string $name, $phrase)
 * @method static array search(string $name, string $keyword, int $limit = 10)
 * @method static Autocomplete connection(?string $name = null)
 * @method static Autocomplete ttl(?int $ttl)
 *
 * @see \Natsumework\RedisAutocomplete\Autocomplete
 */
class Autocomplete extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return \Natsumework\RedisAutocomplete\Autocomplete::class;
    }
}
