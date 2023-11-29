<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Mail\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Mail\NullMailer;
use Tobento\Service\Mail\MailerInterface;
use Tobento\Service\Mail\Message;

/**
 * NullMailerTest
 */
class NullMailerTest extends TestCase
{
    public function testMailerInterfaceMethods()
    {
        $mailer = new NullMailer(name: 'null');
        
        $this->assertInstanceof(MailerInterface::class, $mailer);
        $this->assertSame('null', $mailer->name());
        
        $message = (new Message())
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Subject')
            ->html('<p>Lorem Ipsum</p>');
        
        $mailer->send($message);
        
        $this->assertTrue(true);
    }
}