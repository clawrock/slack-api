Slash commands and dispatcher
=============================

Basic dispatcher
----------------

This application allows to respond to Slack's Slash Commands. In order to make one you have to meet following requirements

1. You need a website with SSL encryption. Please read [this](https://api.slack.com/slash-commands#ssl) Slack's API doc.
2. You must register your app posting /apps Slash Commands on your channel. When you do so, save your token, it's needed to run a dispatcher.
 
Let's start with a new Dispatcher object.

```php
use ClawRock\Slack\SlackFactory;
use ClawRock\Slack\Common\Enum\ResponseType;
use ClawRock\Slack\Fluent\Response\ResponseBuilder;
use ClawRock\Slack\Logic\Request\RequestInterface;



$dispatcher = SlackFactory::dispatcher();
$command    = SlackFactory::slashCommand('your-command-token');
$command->run(function (RequestInterface $req, ResponseBuilder $res) {
    $res->addText('Hello world from dispatcher'); // you can compose your own message here.
});
$messageDataBuilder = $dispatcher->addCommand($command)->dispatch(SlackFactory::getRequest());
$messageDataBuilder->create()->toResponse()->serve();
```

Please note that this message will be ephemeral. You can set it to public if you use:

```php
$messageDataBuilder->setResponseType(ResponseType::IN_CHANNEL())->create()->toResponse()->serve();
```
