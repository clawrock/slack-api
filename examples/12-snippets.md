Pure copy/paste example
========================

You can send snippets to the channel using slack's file.upload endpoint. A snippet may be an existing data or newly created
string that contains code in many different languages.

Dynamic file
========================

In order to create a snippet from string you can use SlackFactory but first you need to
[Generate a slack\'s token](https://api.slack.com/custom-integrations/legacy-tokens)

If you have one you can proceed to call the api's endpoint
```php
use ClawRock\Slack\SlackFactory;
use ClawRock\Slack\Common\Enum\FileType;

$snippetBuilder = SlackFactory::snippet('YOUR-TOKEN');

$snippet = $snippetBuilder->setContent('Test content', FileType::PLAIN_TEXT())
        ->setDestination(['TestUserId', 'TestChannelId'])
        ->addChannel('TestChannel')
        ->addUser('TestUser')
        ->setInitialComment('Initial comment')
        ->setTitle('Test title')
        ->create()
        ->send();
```

This code will send a snippet to 4 different places. It is:

`TestUserId, TestChannelId, TestChannel, TestUser.`

There are multiple FileTypes, you can find them all in FileType class.

Existing file
========================

There is also possibility to send a file from your computer. You can do it similar to the dynamic file:

```php
use ClawRock\Slack\SlackFactory;

$snippetBuilder = SlackFactory::snippet('YOUR-TOKEN');

$path = 'YOUR FILE PATH';

$snippet = $snippetBuilder
    ->setFile($path)
    ->addChannel('TestChannel')
    ->create()
    ->send;
```

This will send a file from the destination you provide in setFile. This file must be readable.

Caveat Emptor
------------------------

Using setFile will override setContent and vice versa. This means that using each other in one builder will change the behaviour.
