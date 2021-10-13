<?php

declare(strict_types=1);

namespace Paymefy\Component\Notifier\Bridge\LabsMobile\Message;

use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\MessageOptionsInterface;

final class LabsMobileMessage implements MessageInterface
{
    private $message;
    private $recipient;
    private $options;

    public function __construct(
        string $message,
        string $recipient,
        MessageOptionsInterface $options
    ) {
        $this->message = $message;
        $this->recipient = $recipient;
        $this->options = $options;
    }

    public function getRecipientId(): ?string
    {
        return $this->recipient;
    }

    public function getSubject(): string
    {
        return $this->message;
    }

    public function getOptions(): ?MessageOptionsInterface
    {
        return $this->options;
    }

    public function getTransport(): ?string
    {
        return 'sms';
    }
}
