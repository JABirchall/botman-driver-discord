<?php


namespace JABirchall\BotMan\Drivers\Discord;

use CharlotteDunois\Yasmin\Client;
use BotMan\BotMan\BotMan;
use CharlotteDunois\Yasmin\Models\Message;
use React\EventLoop\LoopInterface;
use BotMan\BotMan\Cache\ArrayCache;
use BotMan\BotMan\Interfaces\CacheInterface;
use BotMan\BotMan\Interfaces\StorageInterface;
use BotMan\BotMan\Storages\Drivers\FileStorage;

class Factory
{
    /**
     * @param array $config
     * @param LoopInterface $loop
     * @param CacheInterface|null $cache
     * @param StorageInterface|null $storageDriver
     * @return BotMan
     * @throws \Exception
     */
    public function createForDiscord(array $config, LoopInterface $loop, CacheInterface $cache = null, StorageInterface $storageDriver = null)
    {
        $client = new Client($config['discord']['options'], $loop);
        return $this->createUsingDiscord($config, $client, $cache, $storageDriver);
    }
    /**
     * Create a new BotMan instance.
     *
     * @param array $config
     * @param Client $client
     * @param CacheInterface $cache
     * @param StorageInterface $storageDriver
     * @return BotMan
     * @internal param LoopInterface $loop
     */
    public function createUsingDiscord(array $config, Client $client, CacheInterface $cache = null, StorageInterface $storageDriver = null)
    {
        if (empty($cache)) {
            $cache = new ArrayCache();
        }

        if (empty($storageDriver)) {
            $storageDriver = new FileStorage(__DIR__);
        }

        $driver = new DiscordDriver($config, $client);
        $botman = new BotMan($cache, $driver, $config, $storageDriver);

        $client->on('message', function (Message $message) use ($botman) {
            $botman->listen();
        });

        $client->on('ready', function () use ($driver) {
            $driver->connected();
        });

        return $botman;
    }

}