Basic Slash Command usage
-------------------------

Here we use single command with an anonymous function
to respond to the Slack's Slash Command API request.
It is important to set proper command's token
otherwise you will get 'command not found' error response.

_Please_ note, that run() function accepts any [callable](http://php.net/manual/en/language.types.callable.php) object and provides implementation of ResponseInterface ($request) and MessageDataBuilderInterface ($response) for nested callables. 

```php
use ClawRock\Slack\SlackFactory;
use ClawRock\Slack\Fluent\Response\ResponseBuilder;
use ClawRock\Slack\Logic\Request\RequestInterface;

$dispatcher = SlackFactory::dispatcher();

$command = SlackFactory::slashCommand('your-command-token', '/your-command (optional)')
        ->run(function (RequestInterface $request, ResponseBuilder $responseBuilder) {
            $responseBuilder->addText('Hello world');
        });

$messageDataBuilder = $dispatcher
    ->addCommand($command)
    ->dispatch(SlackFactory::getRequest());

$messageData = $messageDataBuilder->create();

$response = $messageData->toResponse();

$response->serve();
```

You can get your own _TOKEN_ when creating apps on slack. For slash command you can post /apps Slash Commands on channel and create a new command for yourself.
