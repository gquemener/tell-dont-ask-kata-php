<?php

declare(strict_types=1);

namespace Pitchart\TellDontAskKata\UseCase;

final readonly class OrderShipmentRequest
{
    public function __construct(
        public int $id,
    ) {
    }
}
