<?php

declare(strict_types=1);

namespace Pitchart\TellDontAskKata\UseCase;

final readonly class OrderApprovalRequest
{
    public function __construct(
        public int $id,
        public bool $approved,
    ) {
    }
}
