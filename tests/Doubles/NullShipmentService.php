<?php

declare(strict_types=1);

namespace Tests\Pitchart\TellDontAskKata\Doubles;

use Pitchart\TellDontAskKata\Domain\Order;
use Pitchart\TellDontAskKata\Services\ShipmentService;

final class NullShipmentService implements ShipmentService
{
    public function ship(Order $order): void
    {
    }

    public function getShippedOrder(): ?Order
    {
        return null;
    }
}
