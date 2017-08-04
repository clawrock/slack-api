Building message
=========================

When you want to respond to slack's API request or send message via Incoming Webhook, you should use MessageDataBuilder. 
It allows you to create powerful messages. To create one we will use SlackFactory.

Using incoming webhooks
-----------------

If you want to send message to the Slack's API webhook you can compose messageDataBuilder either from a dispatcher like you would normally do or create it by yourself.
 
Here is an example on how to make such message.
 
```php
use ClawRock\Slack\SlackFactory;

$messageDataBuilder = $messageDataBuilder = SlackFactory::getMessageDataBuilder();

$messageData = $messageDataBuilder->setText('Incoming webhook example')
    ->setEmoji(':nerd_face:')
    ->setUsername('example')
    ->create();

$message = $messageData->toMessage('https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX');
$message->send();
```

This will render following message:

![Incoming message image](images/02_message.png "Example of incoming webhook message")

You can get _WEBHOOK URL_ when you post /apps Incoming Webhooks on your channel chat.
