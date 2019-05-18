<?php

namespace JABirchall\BotMan\Drivers\Discord;

use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Interfaces\DriverEventInterface;
use BotMan\BotMan\Interfaces\DriverInterface;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Users\User;
use CharlotteDunois\Yasmin\Client;
use CharlotteDunois\Yasmin\Models\Message;
use CharlotteDunois\Yasmin\Models\MessageEmbed;
use Illuminate\Support\Collection;
use React\Promise\PromiseInterface;

class DiscordDriver implements DriverInterface
{
    /** @var Message */
    protected $message;
    /** @var Collection */
    protected $event;
    /** @var Collection */
    protected $config;
    /** @var Client */
    protected $client;
    /** @var string */
    protected $bot_id;
    /** @var string */
    const DRIVER_NAME = 'Discord';

    protected $file;

    public function __construct(array $config, Client $client)
    {
        $this->event = Collection::make();
        $this->config = Collection::make($config['discord']);
        $this->client = $client;

        $this->client->on('message', function (Message $message) {
            $this->message = $message;
        });

        $this->client->login($this->config->get('token'))->done();
    }

    /**
     * Connected event.
     */
    public function connected()
    {
        $this->bot_id = $this->client->user->tag;
    }

    /**
     * Return the driver name.
     *
     * @return string
     */
    public function getName()
    {
        return self::DRIVER_NAME;
    }

    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest()
    {
        return false;
    }

    /**
     * @return bool|DriverEventInterface
     */
    public function hasMatchingEvent()
    {
        return false;
    }

    /**
     * @param IncomingMessage $message
     * @return Answer
     */
    public function getConversationAnswer(IncomingMessage $message)
    {
        return Answer::create($this->message->content ?? null)->setMessage($message);
    }

    /**
     * Retrieve the chat message.
     *
     * @return array
     */
    public function getMessages()
    {
        $messageText = $this->message->content ?? null;
        $user_id = $this->message->author->id ?? null;
        $channel_id = $this->message->channel->id ?? null;
        $message = new IncomingMessage($messageText, $user_id, $channel_id, $this->message);
        $message->setIsFromBot($this->isBot());
        return [$message];
    }

    /**
     * @return bool
     */
    protected function isBot()
    {
        return $this->message->author->bot ?? false;
    }

    /**
     * @param string|Question|IncomingMessage $message
     * @param IncomingMessage $matchingMessage
     * @param array $additionalParameters
     * @return mixed
     */
    public function buildServicePayload($message, $matchingMessage, $additionalParameters = [])
    {
        $payload = [
            'message' => '',
            'embed' => '',
            'originalMessage' => $matchingMessage->getPayload(),
        ];
        if ($message instanceof OutgoingMessage) {
            $payload['message'] = $message->getText();
            $attachment = $message->getAttachment();
            if (!is_null($attachment)) {
                if ($attachment instanceof Image) {
                    $payload['embed'] = new MessageEmbed();
                    $payload['embed']->setImage($attachment->getUrl());
                }
            }
        } else {
            $payload['message'] = $message;
        }
        return $payload;
    }

    /**
     * @param mixed $payload
     * @return PromiseInterface
     */
    public function sendPayload($payload)
    {
        if (empty($this->message)) {
            return null;
        }

        return $payload['originalMessage']->channel->send($payload['message'], ['embed' => $payload['embed']]);
    }

    /**
     * @return bool
     */
    public function isConfigured()
    {
        return $this->config->has('token');
    }

    /**
     * Send a typing indicator.
     * @param IncomingMessage $matchingMessage
     * @return mixed
     */
    public function types(IncomingMessage $matchingMessage)
    {
        $matchingMessage->getPayload()->channel->startTyping();
    }

    /**
     * Retrieve User information.
     * @param IncomingMessage $matchingMessage
     * @return User
     */
    public function getUser(IncomingMessage $matchingMessage)
    {
        $user = $this->client->users->get($matchingMessage->getSender());

        if (!is_null($user)) {
            return new User($matchingMessage->getSender(), '', '', $user->username);
        }

        return new User($this->message->author->id, '', '', $this->message->author->username);
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Low-level method to perform driver specific API requests.
     *
     * @param $endpoint
     * @param array $parameters
     * @param IncomingMessage $matchingMessage
     * @return PromiseInterface
     */
    public function sendRequest($endpoint, array $parameters, IncomingMessage $matchingMessage)
    {
        return false;
    }

    /**
     * Tells if the stored conversation callbacks are serialized.
     *
     * @return bool
     */
    public function serializesCallbacks()
    {
        return false;
    }

    /**
     * Load factory extensions.
     */
    public static function loadExtension()
    {
        $factory = new Factory();
        BotManFactory::extend('createForDiscord', [$factory, 'createForDiscord']);
        BotManFactory::extend('createUsingDiscord', [$factory, 'createUsingDiscord']);
    }
}
