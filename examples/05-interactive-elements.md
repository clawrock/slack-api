Interactive buttons
-------------------

Slack's API allows you to create buttons to communicate with user. This however requires to register a new application on Slack to link buttons with service. You can find step-by-step tutorial in next example. For now, lets focus on creating interactive buttons.

First, you have to create an Attachment with Actions.

```php
use ClawRock\Slack\SlackFactory;
use ClawRock\Slack\Common\Enum\ActionStyle;

$attachmentBuilder = SlackFactory::getAttachmentBuilder();
$attachmentBuilder->setFallback('Simple fallback text')
    ->setCallbackId('asd_123')
    ->createButton()
        ->setName('action1')
        ->setText('Action #1 Button #1')
        ->setValue('action1_button1')
    ->end()
    ->createButton()
        ->setName('action1')
        ->setText('Action #1 Button #2')
        ->setValue('action1_button2')
    ->end()
    ->createButton()
        ->setName('action2')
        ->setText('Action #2 Button #1')
        ->setValue('action2_button1')
        ->setStyle(ActionStyle::DANGER())
        ->setConfirm('Action #2 Button #1','Do you want to continue, this will send request to the service', 'Yup', 'Nah')
    ->end();
```

Now you need to send this message to slack via Incoming Message or in Response to Slash Command.

We will use Incoming Message for now.

```php
// ADD CODE FROM ABOVE

$attachment = $attachmentBuilder->create();

$messageDataBuilder = SlackFactory::getMessageDataBuilder();
$messageDataBuilder->setText('Message with actions')
    ->setUsername('Incoming Message')
    ->addAttachment($attachment);

$messageData = $messageDataBuilder->create();
$message = $messageData->toMessage('https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX');
$message->send();
```

This will render following message:

![Message with attachments](images/05_buttons.png "Buttons in slack message")

And if you click on the red one ("Action #2 Button #1) you will get the confirmation dialog like this:

![Confirmation dialog for slack interactive button](images/05_button-confirm.png "Confirmation dialog for Action #2")

**IMPORTANT!** As mentioned before, those buttons do not make any action yet. You need to create your own application to handle button communiation which will be covered in the next example.

Interactive menus
-------------------

There are also another type of interactive element which is menu. You can create it like that:

```php
use ClawRock\Slack\SlackFactory;
use ClawRock\Slack\Common\Enum\ActionStyle;

$attachmentBuilder = SlackFactory::getAttachmentBuilder();
$attachmentBuilder->setFallback('Simple fallback text')
    ->setCallbackId('asd_123')
    ->createMenu()
        ->setText('text')
        ->setName('name')
        ->addOption('lorem', 'ipsum')
        ->addOption('dolor', 'sit')
        ->end()
    ->end();
``` 

This will create menu with two options, lorem and dolor. 

Instead of static source you can also use dynamic values. Currently there are 5 types of sources:

'users', 'conversations', 'channels', 'external', 'static' 

static is the default value. You can use it like this.

```php
use ClawRock\Slack\SlackFactory;
use ClawRock\Slack\Common\Enum\ActionStyle;
use ClawRock\Slack\Common\Enum\MenuSource;

$attachmentBuilder = SlackFactory::getAttachmentBuilder();
$attachmentBuilder->setFallback('Simple fallback text')
    ->setCallbackId('asd_123')
    ->createMenu()
        ->setText('text')
        ->setName('name')
        ->setSource(MenuSource::CONVERSATIONS())
        ->end()
     ->end();
``` 

*REMEMBER* if you use source other than static, option values will not be available.


In order to get more information about sources please refer to the [Slack API docs](https://api.slack.com/docs/message-menus);

