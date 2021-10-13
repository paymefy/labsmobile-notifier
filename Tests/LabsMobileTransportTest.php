<?php

/*
 * (c) Paymefy <hello@paymefy.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Paymefy\Component\Notifier\Bridge\LabsMobile\Tests;

use Paymefy\Component\Notifier\Bridge\LabsMobile\LabsMobileTransport;
use Paymefy\Component\Notifier\Bridge\LabsMobile\Message\LabsMobileMessage;
use Paymefy\Component\Notifier\Bridge\LabsMobile\Message\LabsMobileMessageOptions;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Test\TransportTestCase;
use Symfony\Component\Notifier\Transport\TransportInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class LabsMobileTransportTest extends TransportTestCase
{
    /**
     * @return LabsMobileTransport
     */
    public function createTransport(?HttpClientInterface $client = null): TransportInterface
    {
        return new LabsMobileTransport('accountSid', 'authToken', $client ?? $this->createMock(HttpClientInterface::class));
    }

    public function toStringProvider(): iterable
    {
        yield ['labsmobile://api.labsmobile.com', $this->createTransport()];
    }

    public function supportedMessagesProvider(): iterable
    {
        yield [new LabsMobileMessage('Hello!','0611223344', new LabsMobileMessageOptions('313513412', 'senderalias'))];
    }

    public function unsupportedMessagesProvider(): iterable
    {
        yield [new ChatMessage('Hello!')];
        yield [$this->createMock(MessageInterface::class)];
    }
}
