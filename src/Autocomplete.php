<?php

namespace Natsumework\RedisAutocomplete;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class Autocomplete
{
    /**
     * @var string|null
     */
    protected $defaultConnection;

    /**
     * @var array
     */
    protected $connection = [];

    /**
     * @var int|null
     */
    protected $defaultTtl;

    /**
     * @var array
     */
    protected $ttl = [];

    /**
     * @var string
     */
    protected $prefix;

    /**
     * Autocomplete constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->defaultConnection = $config['connection'];
        $this->prefix = $config['prefix'];
        $this->defaultTtl = $config['ttl'];
    }

    /**
     * @param string $name
     * @param array|\Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $phrases
     */
    public function addPhrases(string $name, $phrases)
    {
        $connection = $this->getConnection();
        $this->cachePhrases($connection, $name, $phrases);
        $this->createIndex($connection, $name, $phrases);
    }

    /**
     * @param string $name
     * @param array|object $phrase
     */
    public function removePhrase(string $name, $phrase)
    {
        $key = $this->getKey($name);
        $connection = $this->getConnection();

        Redis::connection($connection)
            ->pipeline(function ($pipe) use ($key, $phrase) {
                // remove index
                $patterns = explode(' ', $phrase['name']);

                foreach ($patterns as $pattern) {
                    $len = mb_strlen($pattern);
                    $pattern = Str::lower($pattern);

                    for ($i = 0; $i < $len; $i++) {
                        $member = mb_substr($pattern, 0, $i);
                        $index = $key . ':' . $member;

                        $pipe->zRem($index, $phrase['id']);
                    }
                }

                // remove cache
                $pipe->hDel($key, $phrase['id']);
            });
    }

    /**
     * @param string|null $connection
     * @param string $name
     * @param $phrases
     */
    private function cachePhrases(?string $connection, string $name, $phrases)
    {
        $key = $this->getKey($name);
        $ttl = $this->getTtl();

        Redis::connection($connection)
            ->pipeline(function ($pipe) use ($key, $phrases, $ttl) {
                foreach ($phrases as $phrase) {
                    $pipe->hSet($key, $phrase['id'], serialize($phrase));
                }

                if (!is_null($ttl)) {
                    $pipe->expire($key, $ttl);
                }
            });
    }

    /**
     * @param string|null $connection
     * @param string $name
     * @param $phrases
     */
    private function createIndex(?string $connection, string $name, $phrases)
    {
        $key = $this->getKey($name);
        $ttl = $this->getTtl();

        Redis::connection($connection)
            ->pipeline(function ($pipe) use ($key, $phrases, $ttl) {
                foreach ($phrases as $phrase) {
                    $patterns = explode(' ', $phrase['name']);

                    foreach ($patterns as $pattern) {
                        $len = mb_strlen($pattern);
                        $pattern = Str::lower($pattern);

                        for ($i = 1; $i <= $len; $i++) {
                            $member = mb_substr($pattern, 0, $i);

                            $index = $key . ':' . $member;
                            $score = $phrase['score'] ?? 0;

                            $pipe->zAdd($index, $score, $phrase['id']);

                            if (!is_null($ttl)) {
                                $pipe->expire($index, $ttl);
                            }
                        }
                    }
                }
            });
    }

    /**
     * @param string $name
     * @param string $keyword
     * @param int $limit
     * @return array
     */
    public function search(string $name, string $keyword, int $limit = 10)
    {
        $items = [];

        if ($keyword === '') {
            return $items;
        }

        $keyword = Str::lower($keyword);
        $keywords = explode(' ', $keyword);

        $key = $this->getKey($name);
        $keys = [];
        $destination = $key . ':';
        $len = 0;

        $connection = $this->getConnection();

        foreach ($keywords as $keyword) {
            $len++;
            $keys[] = $key . ':' . $keyword;
            if ($len > 1) {
                $destination .= '|';
            }
            $destination .= $keyword;
        }

        $searchMore = true;
        $count = 0;
        $ids = [];

        if ($len > 1) {
            // If there are multiple keywords
            $count = Redis::connection($connection)
                ->zInterstore($destination, $keys);

            Redis::connection($connection)
                ->expire($destination, 60 * 10);

            if ($count > 0) {
                $ids = Redis::connection($connection)
                    ->zRevRange($destination, 0, $limit);
                $items = Redis::connection($connection)
                    ->hMget($key, $ids);
            }

            $searchMore = $count < $limit;
        }

        if ($searchMore) {
            // If there is only one keyword or the number of items has not reached the limit
            $moreIds = Redis::connection($connection)
                ->zRevRange($key . ':' . $keywords[0], 0, $limit);

            // Remove duplication and superfluous
            $moreIdsCount = 0;
            $insufficientCount = $limit - $count;
            foreach ($moreIds as $index => $moreId) {
                if ($moreIdsCount < $insufficientCount) {
                    if (in_array($moreId, $ids)) {
                        unset($moreIds[$index]);
                        continue;
                    }

                    $moreIdsCount++;
                } else {
                    unset($moreIds[$index]);
                }
            }

            if (count($moreIds) > 0) {
                $moreItems = Redis::connection($connection)
                    ->hMget($key, $moreIds);

                foreach ($moreItems as $moreItem) {
                    $items[] = $moreItem;
                }
            }
        }

        foreach ($items as $index => $item) {
            if ($item === false) {
                unset($items[$index]);
                continue;
            }

            $items[$index] = unserialize($item);
        }

        return $items;
    }

    /**
     * @param string|null $name
     * @return $this
     */
    public function connection(?string $name = null)
    {
        $this->connection = [
            'connection' => $name
        ];

        return $this;
    }

    /**
     * @return mixed
     */
    private function getConnection()
    {
        $connection = $this->defaultConnection;

        if (array_key_exists('connection', $this->connection)) {
            $connection = $this->connection['connection'];
            $this->connection = [];
        }

        return $connection;
    }

    /**
     * @param int|null $ttl
     * @return $this
     */
    public function ttl(?int $ttl)
    {
        $this->ttl = [
            'ttl' => $ttl
        ];

        return $this;
    }

    /**
     * @return int|null
     */
    private function getTtl()
    {
        $ttl = $this->defaultTtl;

        if (array_key_exists('ttl', $this->ttl)) {
            $ttl = $this->ttl['ttl'];
            $this->ttl = [];
        }

        return $ttl;
    }

    /**
     * @param string $name
     * @return string
     */
    private function getKey(string $name)
    {
        return $this->prefix . ':' . $name;
    }
}
