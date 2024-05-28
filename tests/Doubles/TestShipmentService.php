<?php

declare(strict_types=1);

namespace Tests\Pitchart\TellDontAskKata\Doubles;

use Pitchart\TellDontAskKata\Domain\Order;
use Pitchart\TellDontAskKata\Services\ShipmentService;

final class TestShipmentService implements ShipmentService
{
    private ?Order $shippedOrder = null;

    public function ship(Order $order): void
    {
        $this->shippedOrder = $order;
    }

    public function getShippedOrder(): ?Order
    {
        return $this->shippedOrder;
    }
}
