<?php

declare(strict_types=1);

namespace Pitchart\TellDontAskKata\UseCase;

final readonly class SellItemRequest
{
    public function __construct(
        public string $productName,
        public int $quantity,
    ) {
    }
}
