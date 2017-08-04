Advanced message data builder
=============================

Linking user and channel names
------------------------------

Slack's API allows you to link users and channel names without knowing user/channel ids.

In order to create such message you can use two methods in MessageDataBuilder.

1. $messageDataBuilder->setLinkNames(true)
   
    If you add '@username' or '#channel', this will allow you to notify user. It's disabled by default.
   
2. $messageDataBuilder->setParse(true)
    
    Using this method will allow slack's to fully parse your message, including linking names and formatting URLs. 

Merging data
------------

You can add or merge data from array or other messageDataBuilder.

```php
use ClawRock\Slack\SlackFactory;

$dispatcher = SlackFactory::dispatcher();

//dispatcher returns new messageDataBuilder. Text field will be composed from nested functions' return values.
$messageDataBuilder1 = SlackFactory::getMessageDataBuilder()
    
$messageDataBuilder2 = SlackFactory::getMessageDataBuilder();

$messageDataBuilder1->addText('Hello world'); 

$messageDataBuilder2->setUsername('example bot')
    ->setEmoji('exclamation');
    
$data = ['text' => ', hello again'];
 
$messageDataBuilder1->mergeData($data);

//If messageDataBuilder2 has any colliding fields then messageDataBuilder #1 has data priority.
$messageDataBuilder1->mergeDataBuilder($messageDataBuilder2);

$response = $messageDataBuilder1->create()->toResponse();
```

And if you call serve() on $respone you will get following json encoded response:

```json
{
    "username":"example bot",
    "icon_emoji":":exclamation:",
    "text":"Hello world , hello again",
    "response_type":"ephemeral"
}
```

`"response_type":"ephemeral"` - as mentioned in previous example, by default, response will be ephemeral. In order to change it use: 
```php
use ClawRock\Slack\Common\Enum\ResponseType;

$response = $messageDataBuilder1->create()->toResponse()->setResponseType(ResponseType::IN_CHANNEL());
```

Merging priority
----------------
In order to change merge priority, you can set second parameter (true by default):

```php
$data = ['text' => 'array', 'icon_emoji=>':exclamation:'];
$messageDataBuilder->setText('builder')->setEmoji('whale');

$messageDataBuilder->mergeData($data, true) // ['text' => 'builder array', 'icon_emoji'=>':whale:'];
$messageDataBuilder->mergeData($data, false) // ['text' => 'array builder', 'icon_emoji'=>':exclamation:'];
```

Text concatenation
------------------

Third parameter allows you to switch off text field concatenation (which is enabled by default).

```php
$data = ['text' => 'array'];
$messageDataBuilder->setText('builder');

$messageDataBuilder->mergeData($data, true, false) // ['text' => 'builder'];
$messageDataBuilder->mergeData($data, false, false) // ['text' => 'array'];
```
