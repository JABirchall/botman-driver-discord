# botman-driver-discord

# NimdaDiscord

## Getting Started

Require the driver into your botman project `composer require jabirchall/botman-driver-discord`

Example
```php
$config = [
    'discord' =>[
        'token' => "your token",

    ],
];

// Load the driver(s) you want to use
DriverManager::loadDriver(\JABirchall\BotMan\Drivers\Discord\DiscordDriver::class);
$loop = Factory::create();
$botman = BotManFactory::createForDiscord($config, $loop);

// Give the bot something to listen for.
$botman->hears('hello', function (BotMan $bot) {
    $bot->reply('Hello yourself.');
});

// start a convosation
$botman->hears('How are you', function (BotMan $bot) {
    $bot->ask("I'm a bot I have no feelings, How about you?", function (Answer $answer) use ($bot) {
        $bot->reply("Thats great, you said: ". $answer->getText());
    });
});

// Start listening
$botman->listen();
$loop->run();
```

## Prerequisities

* Botman 2.*
* PHP version 7.1+
* A discord bot token

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Authors

* **JABirchall** - *Maintainer*

See also the list of [contributors](https://github.com/JABirchall/NimdaDiscord/graphs/contributors) who participated in this project.

## License

This project is licensed under GNU AGPLv3 License - see the [LICENSE](LICENSE) file for details

## Acknowledgments

* [CharlotteDunois](https://github.com/CharlotteDunois)
