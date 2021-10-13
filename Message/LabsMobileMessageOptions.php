<?php

declare(strict_types=1);

namespace Paymefy\Component\Notifier\Bridge\LabsMobile\Message;

use Symfony\Component\Notifier\Message\MessageOptionsInterface;

final class LabsMobileMessageOptions implements MessageOptionsInterface
{
    private $id;
    private $sender;

    public function __construct(
        string $id,
        string $sender
    ) {
        $this->id = $id;
        $this->sender = $sender;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'sender' => $this->sender
        ];
    }

    public function getRecipientId(): ?string
    {
        return $this->id;
    }
}
