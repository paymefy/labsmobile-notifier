<?php

namespace Paymefy\Component\Notifier\Bridge\LabsMobile;

use Paymefy\Component\Notifier\Bridge\LabsMobile\Message\LabsMobileMessage;
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
    private $username, $apiToken;

    public function __construct(string $username, string $apiToken, HttpClientInterface $client = null, EventDispatcherInterface $dispatcher = null)
    {
        $this->username = $username;
        $this->apiToken = $apiToken;

        parent::__construct($client, $dispatcher);
    }

    protected function doSend(MessageInterface $message): SentMessage
    {
        if (!$message instanceof LabsMobileMessage) {
            throw new UnsupportedMessageTypeException(__CLASS__, LabsMobileMessage::class, $message);
        }

        $endpoint = sprintf('https://%s/json/send', $this->getEndpoint());

        $options = $message->getOptions()->toArray();
        $response = $this->client->request('POST', $endpoint, [
            'auth_basic' => [$this->username, $this->apiToken],
            'max_redirects' => 10,
            'timeout' => 30,
            'json' => [
                'test' => false,
                'message' => $message->getSubject(),
                'tpoa' => $options['sender'],
                'recipient' => [[
                    'msisdn' => $message->getPhone(),
                ]],
                'subid' => $options['id'],
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
        return $message instanceof LabsMobileMessage;
    }

    public function __toString(): string
    {
        return sprintf('labsmobile://%s', $this->getEndpoint());
    }
}
