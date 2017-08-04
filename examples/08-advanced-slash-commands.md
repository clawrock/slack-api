Advanced Slash Commands
======================

This example shows how to use slash commands in a more advanced way.

Using regular expression.
------------------------

Let's assume that your command has token TOK01 and may provide multiple parameters like:

```
/config database:db-name delete
```

We can use built-in SlashCommand's regexp matcher. 

```php
use ClawRock\Slack\SlackFactory;

$slashCommand   = SlackFactory::slashCommand('your-command-token'); // 1
$loggerInstance = SlackFactory::getLoggerInstance();
$slashCommand->on('database')// 2
->run(
    SlackFactory::command()// 1
    ->on('/^\:.* /')// 3
    ->run(
        SlackFactory::command()
            ->on('delete') // 4
            ->run(function ($req, $res) {
                //DB backup code.
                $res->addText('Backup created\n');
            })
            ->run(function ($req, $res) use ($loggerInstance) {
                //DB delete code.
                $commandWithParameters = $req->getCommand() . ' ' . $req->getMatchValue();
                $loggerInstance->addWarning('Deleted DB via slack command: ' . $commandWithParameters);
                $res->addText('Database deleted');
            })
            ->on('create')// 5
            ->run(function ($req, $res) use ($loggerInstance) {
                //DB create code.
                $commandWithParameters = $req->getCommand() . ' ' . $req->getMatchValue();
                $loggerInstance->addWarning('New DB created via slack command: ' . $commandWithParameters);
                $res->addText('Database created');
            })
    )
);
```

Now let's talk about those annotations.

1. Only the root SlashCommand requires a token string. Dispatcher uses it to determine which command it should run.

2. If you want to match beginning of the parameter you can provide a string. No need to use regex like '/^database/' (regex for begins with 'database'). 

3. When command matches a regex, it will use preg_split to provide the rest of the text to nested commands. In this example '/^\:.* /' means that string begins with ':' and ends with a single space. SlashCommand will first try to match it against full parameter. 
 
     ```database:db-name delete``` - does not match.
     
     Then it will try to match text left from previous on() command so we will get:
     
     ```:test delete``` - this matches '/^\:.* /'. Now preg_split will provide 'delete' word for further commands.
     
4. After on() command you can call multiple run() methods. It will start in order you defined them.
 
5. You can use multiple on() in SlashCommand. This allows you to build different routes depending on provided parameters.

**IMPORTANT!** Remember, regex always checks _full_ parameter first!. 

Guarding your commands
----------------------

You can use GuardDecorator to deny some user/channel/team access to commands or even whole dispatcher.

Take a look at this example:

```php
use ClawRock\Slack\SlackFactory;
use ClawRock\Slack\Common\Enum\Permission;

$dispatcher = SlackFactory::dispatcher();
$dispatcherGuard = SlackFactory::guard();
$commandGuard = SlackFactory::guard();
$command = SlackFactory::slashCommand('your-command-token');

$dispatcherGuard->allowTeamIds('T12345')
    ->defaultBehavior(Permission::DENY_ALL()); // 1

$commandGuard->denyUserIds('U123')
    ->defaultBehavior(Permission::ALLOW_ALL()); // 2

$command->addGuard($commandGuard)
    ->run(function () {
        echo 'Hello world';
    });

$dispatcher->addGuard($dispatcherGuard)
    ->addCommand($command)
    ->dispatch(SlackFactory::getRequest()); // 3
```

1. Here only team with ID 'T12345' is allowed, you do not have to set DENY_ALL() permissions because guard by default denies everyone.

2. This guard permits everyone but user with ID 'U123'.

3. In this example you will see 'Hello world' only if:

- Request's token == 'TOK01'.
- Request's team ID == 'T12345' and user ID != 'U123'.

Please note, that permission for team ID is set for the whole dispatcher while for user ID is assigned to the single command only. 

Delaying Messages
-----------------

You can delay response for the slack api if the command you're calling is going to take much longer to response than 3 seconds.

If you do so, you can make up to 5 responses within 30 minutes.

```php
use ClawRock\Slack\SlackFactory;
use ClawRock\Slack\Fluent\Response\ResponseBuilder;
use ClawRock\Slack\Logic\Request\RequestInterface;

$dispatcher = SlackFactory::dispatcher();

$command = SlackFactory::slashCommand('your-command-token', '/your-command (optional)')
->run(function (RequestInterface $request, ResponseBuilder $responseBuilder) {
    $responseBuilder->delay($request->getResponseUrl(), 'Please wait (this is optional)');
    sleep(5);
    $responseBuilder->addText('This will show on chat after 5 seconds');
});

$responseBuilder = $dispatcher
->addCommand($command)
->dispatch(SlackFactory::getRequest());

$responseBuilder->createResponseOrDelayedMessage()->send();
```

You can also check if the message is delayed using ```$responseBuilder->isDelayed()``` to handle it by yourself.

