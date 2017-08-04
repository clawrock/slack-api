Slack-API
==============

Introduction
--------------

Slack-API lets you build simple response system for slack's slash command API.

### Installation

For production make sure you have composer installed. Then run:

```bash
composer require clawrock/slack-api
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```


Code Examples
--------------

Basic example with anonymous function.

```php
use ClawRock\Slack\SlackFactory;

//Create new dispatcher
$dispatcher = SlackFactory::dispatcher();


//Add commands to respond to your request
$dispatcher->addCommand(SlackFactory::slashCommand('your-command-token')
          ->run(function ($req, $res){
                $res->addText('Hello world!');
          }))->dispatch(SlackFactory::getRequest())
      ->create()
      ->toRequest()
      ->serve();
```

You can also add Guard to manage the user/team/channel permissions.

```php
use ClawRock\Slack\Enums\Permissions;

$dispatcher->addGuard(SlackFactory::guard()
        ->defaultBehavior(Permissions::DenyAll())
        ->allowUserIds(['U01'])
        ->allowTeamIds(['T01']))
    ->addCommand(//commands)
    ->dispatch(SlackFactory::getRequest())
    ->create()
    ->toRequest()
    ->serve();
```

It's worth to notice that ***every*** callable object can be used in addCommand() method.

You can send messages via [Incoming webhooks](https://api.slack.com/incoming-webhooks "Slack's API Reference")
```php
use ClawRock\Slack\SlackFactory;

SlackFactory::getMessageService('')->sendText("Hello world!");
```

Documentation
--------------

There are very well documented examples in examples/ directory. You should take a look on them.

Tests
--------------
Simply run
```bash
vendor/bin/phpunit
```

### Requirements ###

Slack API requires server with SSL enabled. Please refer to the Slack's API [reference](https://api.slack.com/slash-commands#ssl) to get more informations.

### Contributing

If you wish to participate in the development, you may use grunt to generate the documentation files. To do so make sure you have node and npm installed.

```
$ node -v
v4.7.0
$ npm -v
2.15.11
```

Then run

```bash
$ npm install
$ grunt init
```

And then to generate docs run

```bash
grunt build
```

This will also launch unit tests.


Credits
--------------

Slack-API was initiated with [generator-composer](https://github.com/T1st3/generator-composer), a [Yeoman](http://yeoman.io) generator that builds a PHP Composer project.

This project uses the following as development dependencies:

* [PHPUnit](http://phpunit.de/)
* [PhpDocumentor](http://phpdoc.org)
* [Php Copy/Paste Detector](https://github.com/sebastianbergmann/phpcpd)


License
--------------

Author: cr-team <office@clawrock.com>
