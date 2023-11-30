# Mail Service

Mailer interface for PHP applications using the [Symfony Mailer](https://github.com/symfony/mailer) as default mailer implementation.

## Table of Contents

- [Getting started](#getting-started)
    - [Requirements](#requirements)
    - [Highlights](#highlights)
- [Documentation](#documentation)
    - [Basic Usage](#basic-usage)
        - [Creating And Sending Messages](#creating-and-sending-messages)
    - [Message](#message)
        - [Email Addresses](#email-addresses)
        - [Contents](#contents)
        - [Headers](#headers)
        - [File Attachments](#file-attachments)
        - [Tags And Metadata](#tags-and-metadata)
        - [Queue](#queue)
        - [Send With Mailer](#send-with-mailer)
        - [Custom Parameters](#custom-parameters)
    - [Mailer](#mailer)
        - [Null Mailer](#null-mailer)
        - [SF Mailer](#sf-mailer)
    - [Mailers](#mailers)
        - [Default Mailers](#default-mailers)
        - [Lazy Mailers](#lazy-mailers)
    - [Templating](#templating)
        - [Writing Views](#writing-views)
        - [Render Templates](#render-templates)
    - [Events](#events)
    - [Symfony](#symfony)
        - [Symfony Mailer](#symfony-mailer)
            - [Events Support](#events-support)
            - [Queue Support](#queue-support)
            - [Default Addresses And Parameters](#default-addresses-and-parameters)
            - [Html To Text Converting](#html-to-text-converting)
        - [Symfony Dsn Mailer Factory](#symfony-dsn-mailer-factory)
        - [Symfony Smtp Mailer Factory](#symfony-smtp-mailer-factory)
        - [Symfony Custom Parameters Support](#symfony-custom-parameters-support)
- [Credits](#credits)
___

# Getting started

Add the latest version of the mail service project running this command.

```
composer require tobento/service-mail
```

## Requirements

- PHP 8.0 or greater

## Highlights

- Framework-agnostic, will work with any project
- Decoupled design

# Documentation

## Basic Usage

### Creating And Sending Messages

```php
use Tobento\Service\Mail\MailerInterface;
use Tobento\Service\Mail\Message;

class SomeService
{
    public function send(MailerInterface $mailer): void
    {
        $message = (new Message())
            ->from('from@example.com')
            ->to('to@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('replyto@example.com')
            ->subject('Subject')
            //->textTemplate('welcome-text')
            //->htmlTemplate('welcome')
            //->text('Lorem Ipsum')
            ->html('<p>Lorem Ipsum</p>');

        $mailer->send($message);
    }
}
```

Check out the available [Mailers](#mailer).

Check out the [Message](#message) to learn more about it.

## Message

### Email Addresses

```php
use Tobento\Service\Mail\Message;
use Tobento\Service\Mail\Address;

$message = (new Message())
    // email address as a simple string:
    ->from('from@example.com')
    
    // email address and name (optional) as object:
    ->from(new Address('from@example.com', 'Name'))
    
    ->replyTo('replyto@example.com')
    
    // the following methods support multiple addresses
    // as strings or objects:
    ->to('to@example.com', 'anotherTo@example.com')
    
    ->to(
        new Address('to@example.com'),
        new Address('anotherTo@example.com')
    )
    
    ->cc('cc@example.com', new Address('anotherCc@example.com'))
    
    ->bcc('bcc@example.com', new Address('anotherBcc@example.com'));
```

### Contents

```php
use Tobento\Service\Mail\Message;
use Tobento\Service\Mail\Template;

$message = (new Message())
    // content defined as a string:
    ->subject('Subject')
    ->text('Lorem Ipsum')
    ->html('<p>Lorem Ipsum</p>')
    
    // content defined with a template object:
    ->text(new Template(
        name: 'welcome-text',
        data: ['name' => 'John'],
    ))
    
    ->html(new Template('welcome', []))    
    
    // using template methods:
    ->textTemplate(name: 'welcome-text', data: [])
    
    ->htmlTemplate('welcome', []);
```

### Headers

```php
use Tobento\Service\Mail\Message;
use Tobento\Service\Mail\Parameter;
use Tobento\Service\Mail\Address;

$message = (new Message())
    // Text header:
    ->parameter(new Parameter\TextHeader(
        name: 'X-Custom-Header',
        value: 'value',
    ))
    
    // Id header:
    ->parameter(new Parameter\IdHeader(
        name: 'References',
        ids: ['a@example.com', 'b@example.com'],
    ))
    
    // Path header:
    ->parameter(new Parameter\PathHeader(
        name: 'Return-Path',
        address: 'return@example.com',
        
        // or as object
        // address: new Address('return@example.com'),
    ));
```

### File Attachments

```php
use Tobento\Service\Mail\Message;
use Tobento\Service\Mail\Parameter;
use Tobento\Service\Filesystem\File;
use Psr\Http\Message\StreamInterface;

$message = (new Message())
    // File defined as string:
    ->parameter(new Parameter\File(
        file: '/path/to/document.pdf',
        
        // optional parameters:
        filename: 'Document',
        mimeType: 'application/pdf',
    ))

    // File defined with File object:
    ->parameter(new Parameter\File(
        file: new File('/path/to/document.pdf'),
    ))

    // StreamFile:
    ->parameter(new Parameter\StreamFile(
        stream: $stream, // StreamInterface
        filename: 'Filename.png',
        
        // optional parameters:
        mimeType: 'image/png',        
    ))
    
    // ResourceFile:
    ->parameter(new Parameter\ResourceFile(
        resource: fopen('/path/to/image.png', 'r+'),
        filename: 'Image.png',
        
        // optional parameters:
        mimeType: 'image/png',        
    ));    
```

### Tags And Metadata

```php
use Tobento\Service\Mail\Message;
use Tobento\Service\Mail\Parameter;

$message = (new Message())
    // Tags:
    ->parameter(new Parameter\Tags(['tagname']))
    
    // Metadata:
    ->parameter(new Parameter\Metadata([
        'name' => 'value',
    ]));
```

### Queue

You may queue your message if your mailer is configured to support it.

```php
use Tobento\Service\Mail\Message;
use Tobento\Service\Mail\Parameter;

$message = (new Message())
    ->parameter(new Parameter\Queue(
        // you may specify the queue to be used:
        name: 'secondary',
        
        // you may specify a delay in seconds:
        delay: 30,
        
        // you may specify how many times to retry:
        retry: 3,
        
        // you may specify a priority:
        priority: 100,
        
        // you may specify if you want to encrypt the message:
        encrypt: true,
        
        // you may specify if you want to render the message templates
        // before queuing:
        renderTemplates: false, // true default
    ));
```

Check out the [Symfony Mailer - Queue Support](#queue-support) for support.

### Send With Mailer

You may define a mailer used to send the message if your mailer supports it.

Check out the [Mailers](#mailers) for more detail.

```php
use Tobento\Service\Mail\Message;
use Tobento\Service\Mail\Parameter;

$message = (new Message())
    ->parameter(new Parameter\SendWithMailer(name: 'mailchimp'));
```

### Custom Parameters

You may write your own parameters in the following way:

```php
use Tobento\Service\Mail\ParameterInterface;

class CustomParameter implements ParameterInterface
{
    /**
     * Create a new CustomParameter.
     *
     * @param string $name
     */
    public function __construct(
        protected string $name
    ) {}
    
    /**
     * Returns the name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
}
```

Check out the [Symfony Custom Parameters Support](#symfony-custom-parameters-support) for handling your custom parameter.

## Mailer

### Null Mailer

The ```NullMailer::class``` does not send any mail message at all which may be useful while developing (or testing).

```php
use Tobento\Service\Mail\NullMailer;
use Tobento\Service\Mail\MailerInterface;

$mailer = new NullMailer(name: 'null');

var_dump($mailer instanceof MailerInterface);
// bool(true)
```

### SF Mailer

Documentation is in the [Symfony Mailer](#symfony-mailer) section.

## Mailers

You may use the following mailers as your MailerInterface implementation for supporting the [Send With Mailer](#send-with-mailer) parameter.

### Default Mailers

```php
use Tobento\Service\Mail\Mailers;
use Tobento\Service\Mail\MailersInterface;
use Tobento\Service\Mail\MailerInterface;

$mailers = new Mailers(
    $mailer, // MailerInterface
    $anotherMailer, // MailerInterface
);

var_dump($mailers instanceof MailersInterface);
// bool(true)

var_dump($mailers instanceof MailerInterface);
// bool(true)
```

### Lazy Mailers

The lazy mailers class creates the mailer only on demand.

```php
use Tobento\Service\Mail\LazyMailers;
use Tobento\Service\Mail\MailersInterface;
use Tobento\Service\Mail\MailerInterface;
use Tobento\Service\Mail\MailerFactoryInterface;
use Tobento\Service\Mail\Symfony;
use Psr\Container\ContainerInterface;

$mailers = new LazyMailers(
    container: $container, // ContainerInterface
    mailers: [
        // using a factory:
        'default' => [
            // factory must implement MailerFactoryInterface
            'factory' => Symfony\SmtpMailerFactory::class,
            
            'config' => [
                'encryption' => '',
                'host' => 'host',
                'user' => 'user',
                'password' => '********',
                'port' => 465,

                // you may define default addresses and parameters
                // or set to null if defaults are used from email factory.
                'defaults' => [
                    'from' => 'from@example.com',
                ],
            ],
        ],
        
        // using a closure:
        'secondary' => static function (string $name, ContainerInterface $c): MailerInterface {
            // create mailer ...
            return $mailer;
        },
        
        'mailchimp' => [
            // ...
        ],
    ],
);

var_dump($mailers instanceof MailersInterface);
// bool(true)

var_dump($mailers instanceof MailerInterface);
// bool(true)
```

## Templating

The following examples are aimed for the default renderer ```Tobento\Service\Mail\ViewRenderer::class```.

### Writing Views

```php
use Tobento\Service\Mail\Message;

$message = (new Message())
    //...
    ->htmlTemplate(
        name: 'email/welcome',
        data: ['name' => 'John', 'text' => 'Lorem ipsum'],
    );
```

**The welcome view template**

A variable called message, which is an instance of ```Tobento\Service\Mail\TemplateMessageInterface::class``` is available on every view.

Furthermore, use css file assests to design your template. When the template gets rendered, it will convert them to inline styles for better email clients support.

```php
<!DOCTYPE html>
<html>
    <head>
        <title><?= $view->esc($message->subject()) ?></title>

        <?php
        // render assets only if inline styles are not used:
        if (!$withInlineCssStyles) {
            echo $view->assets()->render();
        }
        ?>

        <?php
        // assets can be included in every subview too.
        $view->asset('email.css');
        ?>
    </head>
    <body>
        <?= $view->render('email/header') ?>

        <h1>Hellow <?= $view->esc($name) ?></h1>

        <p><?= $view->esc($text) ?></p>
        
        <img src="<?= $message->embed('path/to/image.jpg') ?>">
        
        <?php
        // embed from stream: Psr\Http\Message\StreamInterface
        // <img src="<?= $message->embed(file: $stream, mimeType: 'image/jpeg') ?>">
        
        // embed from file: Tobento\Service\Filesystem\File
        // <img src="<?= $message->embed($file) ?>">        
        ?>

        <?= $view->render('email/footer') ?>
    </body>
</html>
```

### Render Templates

You may want to render a template for an email web view, debugging or other purposes.

```php
use Tobento\Service\Mail\RendererInterface;
use Tobento\Service\Mail\TemplateInterface;
use Tobento\Service\Mail\Template;
use Tobento\Service\Mail\Message;

class SomeController
{
    public function renderEmail(RendererInterface $renderer): string
    {
        // by using a template object:
        $content = $renderer->renderTemplate(
            template: new Template(
                name: 'email/welcome',
                data: ['name' => 'John'],
            ),
            
            // you may not want to convert css
            // to inline styles for web views
            // as you might use CSP with blocking inline css.
            withInlineCssStyles: false, // true is default
        );
        
        // render message contents:
        $message = (new Message())
            ->htmlTemplate('email/welcome', ['name' => 'John']);
        
        if ($message->getHtml() instanceof TemplateInterface) {
            $content = $renderer->renderTemplate($message->getHtml());
        }
        
        return $content;
    }
}
```

## Events

You may listen to the following events if your mailer is configured to support it.

| Event | Description |
| --- | --- |
| ```Tobento\Service\Mail\Event\MessageSent::class``` | The Event will be fired after sending the message. |
| ```Tobento\Service\Mail\Event\MessageNotSent::class``` | The Event is fired if the message could not be sent. |
| ```Tobento\Service\Mail\Event\MessageQueued::class``` | The Event will be fired after queuing the message. |

Check out the [Symfony Mailer - Events Support](#events-support) to create the mailer supporting events.

## Symfony

### Symfony Mailer

```php
use Tobento\Service\Mail\MailerInterface;
use Tobento\Service\Mail\Symfony;
use Tobento\Service\Mail\ViewRenderer;
use Tobento\Service\View;
use Tobento\Service\Dir;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;

// create the renderer:
$renderer = new ViewRenderer(
    new View\View(
        new View\PhpRenderer(
            new Dir\Dirs(
                new Dir\Dir('dir/views/'),
            )
        ),
        new View\Data(),
        new View\Assets('dir/src/', 'https://example.com/src/')
    )
);

// create email factory:
$emailFactory = new Symfony\EmailFactory(
    renderer: $renderer,
);

// create the transport:
$transport = (new EsmtpTransportFactory())->create(new Dsn(
    'smtp',
    'host',
    'user',
    'password',
    465,
    [],
));

// create the mailer:
$mailer = new Symfony\Mailer(
    name: 'default',
    emailFactory: $emailFactory,
    transport: $transport,
);

var_dump($mailer instanceof MailerInterface);
// bool(true)
```

Check out the [View Service](https://github.com/tobento-ch/service-view) to learn more about it.

#### Events Support

In order to support events you will need to pass a dispatcher to the mailer:

```php
use Tobento\Service\Mail\Symfony;
use Psr\EventDispatcher\EventDispatcherInterface;

// create the mailer:
$mailer = new Symfony\Mailer(
    name: 'default',
    emailFactory: $emailFactory,
    transport: $transport,
    
    // pass your event dispatcher:
    eventDispatcher: $dispatcher, // EventDispatcherInterface
);
```

#### Queue Support

In order to support queuing messages you will need to pass a queue handler to the mailer.

Consider using the default queue handler using the [Queue Service](https://github.com/tobento-ch/service-queue):

**First, install the queue service:**

```
composer require tobento/service-queue
```

**Next, pass the queue handler to the mailer:**

```php
use Tobento\Service\Mail\Symfony;
use Tobento\Service\Mail\QueueHandlerInterface;
use Tobento\Service\Mail\RendererInterface;
use Tobento\Service\Mail\Queue\QueueHandler;
use Tobento\Service\Queue\QueueInterface;

// create the mailer:
$mailer = new Symfony\Mailer(
    name: 'default',
    emailFactory: $emailFactory,
    transport: $transport,
    
    // pass your queue handler implementing QueueHandlerInterface:
    queueHandler: new QueueHandler(
        queue: $queue, // QueueInterface
        renderer: $renderer, // RendererInterface
        // you may define the default queue used if no specific is defined on the message.
        queueName: 'mails', // null|string
    ),
);
```

**Finally, make sure the container of the [job processor](https://github.com/tobento-ch/service-queue#job-processor) has the following interfaces available:**

Example using the [Service Container](https://github.com/tobento-ch/service-container) as container:

```php
use Tobento\Service\Mail\MailerInterface;
use Tobento\Service\Mail\MessageFactoryInterface;
use Tobento\Service\Mail\MessageFactory;
use Tobento\Service\Queue\JobProcessor;
use Tobento\Service\Container\Container;

$container = new Container();
$container->set(MessageFactoryInterface::class, MessageFactory::class);
$container->set(MailerInterface::class, function() {
    // create mailer:
    return $mailer;
});

$jobProcessor = new JobProcessor($container);
```

#### Default Addresses And Parameters

You may set default addresses and/or parameters to be applied to every message:
    
```php
use Tobento\Service\Mail\Symfony;
use Tobento\Service\Mail\Address;
use Tobento\Service\Mail\Parameters;
use Tobento\Service\Mail\Parameter;

$emailFactory = new Symfony\EmailFactory(
    renderer: $renderer,
    
    // you may pass default addresses or parameters
    // to be applied to every message created.
    config: [
        'from' => 'from@example.com',
        // with object:
        'from' => new Address('from@example.com', 'Name'),
        
        'replyTo' => 'reply@example.com',
        // with object:
        'replyTo' => new Address('reply@example.com'),
        
        // You may define an address to send all emails to:
        'alwaysTo' => 'debug@example.com',
        // with object:
        'alwaysTo' => new Address('debug@example.com'),
        
        'parameters' => new Parameters(
            new Parameter\PathHeader('Return-Path', 'return@example.com'),
        ),
    ],
);

// create the mailer:
$mailer = new Symfony\Mailer(
    name: 'default',
    emailFactory: $emailFactory,
    transport: $transport,
);
```

#### Html To Text Converting

If you create a message without text content, it will be created from your html content.

```php
use Tobento\Service\Mail\Message;

$message = (new Message())
    // will be created from the html:
    //->text('Lorem Ipsum')
    
    ->html('<p>Lorem Ipsum</p>');
```

### Symfony Dsn Mailer Factory

```php
use Tobento\Service\Mail\MailerInterface;
use Tobento\Service\Mail\Symfony;
use Tobento\Service\Mail\ViewRenderer;
use Tobento\Service\Mail\Address;
use Tobento\Service\Mail\Parameters;
use Tobento\Service\Mail\Parameter;
use Tobento\Service\View;
use Tobento\Service\Dir;

// create the renderer:
$renderer = new ViewRenderer(
    new View\View(
        new View\PhpRenderer(
            new Dir\Dirs(
                new Dir\Dir('dir/views/'),
            )
        ),
        new View\Data(),
        new View\Assets('dir/src/', 'https://example.com/src/')
    )
);

// create email factory:
$emailFactory = new Symfony\EmailFactory(
    renderer: $renderer,
);

// create the factory:
$factory = new Symfony\DsnMailerFactory($emailFactory);

// create the mailer:
$mailer = $factory->createMailer(name: 'default', config: [
    'dsn' => 'smtp://user:pass@smtp.example.com:port',
    
    // If the username, password or host contain
    // any character considered special in a URI
    // (such as +, @, $, #, /, :, *, !),
    // use the following instead of dsn above:
    //'scheme' => 'smtp',
    //'host' => 'host',
    //'user' => 'user',
    //'password' => '********',
    //'port' => 465,
    
    // you may define default addresses and parameters
    // or set to null if defaults are used from email factory.
    'defaults' => [
        'from' => 'from@example.com',
    ],
]);

var_dump($mailer instanceof MailerInterface);
// bool(true)
```

### Symfony Smtp Mailer Factory

```php
use Tobento\Service\Mail\MailerInterface;
use Tobento\Service\Mail\Symfony;
use Tobento\Service\Mail\ViewRenderer;
use Tobento\Service\Mail\Address;
use Tobento\Service\Mail\Parameters;
use Tobento\Service\Mail\Parameter;
use Tobento\Service\View;
use Tobento\Service\Dir;

// create the renderer:
$renderer = new ViewRenderer(
    new View\View(
        new View\PhpRenderer(
            new Dir\Dirs(
                new Dir\Dir('dir/views/'),
            )
        ),
        new View\Data(),
        new View\Assets('dir/src/', 'https://example.com/src/')
    )
);

// create email factory:
$emailFactory = new Symfony\EmailFactory(
    renderer: $renderer,
);

// create the factory:
$factory = new Symfony\SmtpMailerFactory($emailFactory);

// create the mailer:
$mailer = $factory->createMailer(name: 'default', config: [
    'encryption' => '',
    'host' => 'host',
    'user' => 'user',
    'password' => '********',
    'port' => 465,
    
    // you may define default addresses and parameters
    // or set to null if defaults are used from email factory.
    'defaults' => [
        'from' => 'from@example.com',
    ],
]);

var_dump($mailer instanceof MailerInterface);
// bool(true)
```

### Symfony Custom Parameters Support

In order to support your custom parameters you could write a new email factory class or extend the default:

```php
use Tobento\Service\Mail\Symfony;
use Tobento\Service\Mail\MessageInterface;
use Tobento\Service\Mail\ParameterInterface;
use Symfony\Component\Mime\Email;

class CustomizedEmailFactory extends Symfony\EmailFactory
{
    /**
     * Create email from message.
     *
     * @param MessageInterface $message
     * @return Email
     */
    public function createEmailFromMessage(MessageInterface $message): Email
    {
        $email = parent::createEmailFromMessage($message);
        
        // filter your custom parameters:
        $parameters = $message->parameters()->filter(
            fn(ParameterInterface $p): bool => $p instanceof CustomParameter
        );
        
        // do something with it:
        foreach($parameters as $parameter) {}
    }
}
```

# Credits

- [Tobias Strub](https://www.tobento.ch)
- [All Contributors](../../contributors)
- [Symfony Mailer](https://github.com/symfony/mailer)