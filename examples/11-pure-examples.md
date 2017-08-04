Pure copy/paste example
========================

```php
use ClawRock\Slack\Common\Enum\ResponseType;
use ClawRock\Slack\Common\CommandParameters;
use ClawRock\Slack\SlackFactory;

$dispatcher = SlackFactory::dispatcher();

$token = 'app-token';

$helpCommand    = SlackFactory::slashCommand($token, '/help');
$doCommand      = SlackFactory::slashCommand($token, '/do');
$defaultCommand = SlackFactory::slashCommand($token);

$helpCommand
    ->run(function ($req, $res) {
        $res->addText('This is the default response if no parameter match');
    })
    ->on('start')
    ->run(function ($req, $res) {
        $res->addText("Help for command '/help start' ");
    });

$doCommand
    ->runAlways(function ($req, $res) {
        //Silently log that someone ran /do command.
    })
    ->on('start')
    ->run(function () {
        //This will run before next function.
    })
    ->run(function ($req, $res) {
        //This response will be visible for everyone
        $res->setResponseType(ResponseType::IN_CHANNEL());
        $res->setText("Someone just typed '/do start' ");
    })
    ->run(function () {
        //This will run after previous function
    });

$defaultCommand
    ->run(function ($req, $res) {
        $res->addText('Default response for specific token but no command match');
    })
    ->on('match')
    ->run(function ($req, $res, CommandParameters $params) {
        $res->addText('Matched command: ' . $params->getMatchingValue() . '\n');
        $res->addText('Command leftover: ' . $params->getTextLeftover() . '\n');
    });

$messageDataBuilder = $dispatcher
    ->addCommand($doCommand)
    ->addCommand($helpCommand)
    ->addCommand($defaultCommand)
    ->dispatch($request);

$messageData = $messageDataBuilder->create();

$response = $messageData->toResponse();
```

This code will produce following response:
=====================================

/help
---------------
``` EPHEMERAL: 'This is the default response if no parameter match' ```

/help foo
---------------
``` EPHEMERAL: 'This is the default response if no parameter match' ```

/help start
---------------
``` EPHEMERAL: 'Help for command '/help start'' ```

/do
---------------
``` EPHEMERAL: '' ```   
_even if we do not have any default function set, it will not show the error 'I don't know this command', this is caused by the runAlways() method_

**will _silently_ execute command inside runAlways()**

/do foo
---------------
``` EPHEMERAL: '' ```

_same as the example above_

**will _silently_ execute command inside runAlways()**

/do start
---------------
``` PUBLIC: 'Someone just typed '/do start'' ```

**will _silently_ execute command inside runAlways()**

/reboot
---------------
``` EPHEMERAL: 'Default response for token if no command match' ```

/run
---------------
``` EPHEMERAL: 'Default response for token if no command match' ```

/run match me a function
---------------
``` 
EPHEMERAL:

'Matched command: match'
'Command leftover: me a function'
```
