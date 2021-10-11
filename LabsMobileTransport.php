<?php

namespace Paymefy\Component\Notifier\Bridge\LabsMobile;

use Symfony\Component\Notifier\Exception\TransportException;
use Symfony\Component\Notifier\Exception\UnsupportedMessageTypeException;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Transport\AbstractTransport;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class LabsMobileTransport extends AbstractTransport
{
    protected const HOST = 'api.labsmobile.com';
    private $username, $apiToken, $from;

    public function __construct(string $username, string $apiToken, string $from, HttpClientInterface $client = null, EventDispatcherInterface $dispatcher = null)
    {
        $this->username = $username;
        $this->apiToken = $apiToken;
        $this->from = $from;

        parent::__construct($client, $dispatcher);
    }

    protected function doSend(MessageInterface $message): SentMessage
    {
        if (!$message instanceof SmsMessage) {
            throw new UnsupportedMessageTypeException(__CLASS__, SmsMessage::class, $message);
        }

        $endpoint = sprintf('https://%s/json/send', $this->getEndpoint());

        $response = $this->client->request('POST', $endpoint, [
            'auth_basic' => [$this->username, $this->apiToken],
            'max_redirects' => 10,
            'timeout' => 30,
            'json' => [
                'test' => false,
                'message' => $message->getSubject(),
                'tpoa' => $this->from,
                'recipient' => [[
                    'msisdn' => $message->getPhone(),
                ]],
                'subid' => 23423,
            ],
            'headers' => [
                'Cache-Control' => 'no-cache',
            ],
        ]);

        $payload = $response->toArray(false);

        if ($payload['code'] !== '0') {
            throw new TransportException('Unable to send the SMS: ' . $payload['message'], $response);
        }

        $sentMessage = new SentMessage($message, (string) $this);
        $sentMessage->setMessageId($payload['subid']);

        return $sentMessage;
    }

    public function supports(MessageInterface $message): bool
    {
        return $message instanceof SmsMessage;
    }

    public function __toString(): string
    {
        return sprintf('labsmobile://%s?from=%s', $this->getEndpoint(), $this->from);
    }
}
