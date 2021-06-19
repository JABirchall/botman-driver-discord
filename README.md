# botman-driver-discord

## Getting Started

### Prerequisites

* Botman 2.*
* PHP version 7.1+
* mbstring
* A discord bot token

### Install the driver

Require the driver into your botman project:

    composer require jabirchall/botman-driver-discord

### Create the Discord bot

1. Enable the *Developer mode* in the *Advanced* tab in Discord settings.
2. In https://discord.com/developers/applications, create a new application:
  - click on the *New Application* button, fill application name and validate;
  - under the *Bot* tab:
    - in *Privileged Gateway Intents* section, check *Presence Intent* and *Server Members Intent* then save;
  - under the *OAuth2* tab:
    - check `bot` in the `Scopes` section,
    - check the permissions you want in the `Bot Permissions` section,
    - then copy the generated authentication url.
3. Paste the authentication url in your web browser, then confirm the dialog popup to attach the bot to a Discord server.

### Test your bot

Copy-paste the following example in a php script and replace `your token` by the actual token that you can copy in your Discord application page under *Bot* tab, in *Build-A-Bot* section.

Then run the script: the Discord bot should be connected and answer every time a user says `hello`.

```php
<?php

require_once ("./vendor/autoload.php");

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use React\EventLoop\Factory;
use BotMan\Drivers\Discord\DiscordDriver;

$config = [
    'discord' =>[
        'token' => "your token",
        'options' => [
            'disableClones' => true,
            'disableEveryone' => true,
            'fetchAllMembers' => false,
            'messageCache' => true,
            'messageCacheLifetime' => 600,
            'messageSweepInterval' => 600,
            'presenceCache' => false,
            'userSweepInterval' => 600,
            'ws.disabledEvents' => [],
        ],
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

// start a conversation
$botman->hears('How are you', function (BotMan $bot) {
    $bot->ask("I'm a bot I have no feelings, How about you?", function (Answer $answer) use ($bot) {
        $bot->reply("Thats great, you said: ". $answer->getText());
    });
});

// Start listening
$botman->listen();
$loop->run();
```

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Authors

* **JABirchall** - *Maintainer*

See also the list of [contributors](https://github.com/JABirchall/NimdaDiscord/graphs/contributors) who participated in this project.

## License

This project is licensed under GNU AGPLv3 License - see the [LICENSE](LICENSE) file for details

## Acknowledgments

* [CharlotteDunois](https://github.com/CharlotteDunois) who worked on [Yasmin](https://github.com/sylae/Yasmin).
