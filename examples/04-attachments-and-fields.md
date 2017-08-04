Attachments and fields
----------------------

In this example we will to create attachments and corresponding fields.

```php
use ClawRock\Slack\SlackFactory;
use ClawRock\Slack\Common\Enum\AttachmentColor;

$attachmentBuilder = SlackFactory::getAttachmentBuilder();
$attachment = $attachmentBuilder
    ->setText('Attachment #1')
    ->setColor(AttachmentColor::CHARTREUSE())
    ->addField('Field #1', 'Value #1', true)
    ->addField('Field #2', 'Value #2', true)
    ->setAuthorName('Author name')
    ->create();

$messageDataBuilder = SlackFactory::getMessageDataBuilder();
$messageData = $messageDataBuilder->setText('Sample Message with Attachments and Fields')
    ->setUsername('Example message')
    ->setEmoji('email')
    ->addAttachment($attachment) // adds previously created attachment
    ->createAttachment() // switches context to the AttachmentBuilder, call end() to back to parent's scope.
        ->setText('Attachment #2')
        ->setColor(AttachmentColor::AQUAMARINE())
    ->end() // switches back to MessageDataBuilder object.
    ->create();
    
$message = $messageData->toMessage('https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX'); 
$message->send();
```

**You must remember** to end building by invoking end() method, otherwise you will stay in AttachmentBuilder's scope.
   
This will create following message

![Incoming message image](images/04_message-with-examples.png "Example message with attachment and fields")
