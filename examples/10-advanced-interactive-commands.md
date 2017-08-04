Advanced interactive commands
=============================

Building interactive buttons
----------------------------

There are some things you need to know about interactive buttons.

1. AttachmentDataBuilder has two methods which has default values.

    Most important setCallbackId(). CallbackId is used to determine which command to run. It serves as SlashCommand's parameters but without regexp checks. This means that your callbackId must have _exact_ match to run command. Take a look at this example:
    
    ```php
    use ClawRock\Slack\SlackFactory;
    
    $slashCommand = SlackFactory::getSlashCommand('your-app-token');
        ->on('your-regexp-for-parameter') // Slash Command's Parameter
        ->run(/* function */);
        
    $interactiveCommand = SlackFactory::getInteractiveCommand('your-app-token')
        ->on('callbackId') // Interactive Command's Callback ID
        ->run(/* function */);
    ```

    The other one is less important. It's setFallback($text). According to the docs:

    >A plaintext message displayed to users using an interface that does not support attachments or interactive messages. Consider leaving a URL pointing to your service if the potential message actions are representable outside of Slack. Otherwise, let folks know what they are missing.

2. There is a helper Answer class available from Factory's answer($name, $value) method. It allows you to control which button was clicked. It does not have inner logic except for simple if.

    ```php
    use ClawRock\Slack\SlackFactory;
    
    $command = SlackFactory::interactiveCommand('your-app-token');
    $command->on('marry-question')
        ->run(SlackFactory::answer('marry-me', 'yes')
            ->setRun(
                function ($req, $res) {
                    $res->setEmoji('smile');
                    $res->addText('Good');
                }
            ))
        ->run(SlackFactory::answer('marry-me', 'no')
            ->setRun(
                function ($req, $res) {
                    $res->setEmoji('cry');
                    $res->addText('Bad');
                }
            )
        );
    $dispatcher = SlackFactory::dispatcher();
    $dispatcher->addCommand($command);
    $dispatcher->dispatch(SlackFactory::getRequest());
    ```

Dealing with the original message
--------------------------------

Slack API allows you to manipulate the original action message.

There are two methods in MessageDataBuilder to handle it.

1. setReplaceOriginal(), according to slack docs:

    >Used only when creating messages in response to a button action invocation. When set to true, the inciting message will be replaced by this message you're providing. When false, the message you're providing is considered a brand new message.

2. setDeleteOriginal():

    >Used only when creating messages in response to a button action invocation. When set to true, the inciting message will be deleted and if a message is provided, it will be posted as a brand new message.

If you want to get more information about interactive buttons, please see [Responding to buttons](07-responding-to-buttons.md) example.

