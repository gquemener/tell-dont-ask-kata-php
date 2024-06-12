<?php

declare(strict_types=1);

namespace Pitchart\TellDontAskKata\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Pitchart\TellDontAskKata\Services\ShipmentService;
use Pitchart\TellDontAskKata\UseCase\ApprovedOrderCannotBeRejectedException;
use Pitchart\TellDontAskKata\UseCase\OrderCannotBeShippedException;
use Pitchart\TellDontAskKata\UseCase\OrderCannotBeShippedTwiceException;
use Pitchart\TellDontAskKata\UseCase\RejectedOrderCannotBeApprovedException;
use Pitchart\TellDontAskKata\UseCase\ShippedOrdersCannotBeChangedException;

final class Order
{
    private int $id;

    private OrderStatus $status;

    private float $tax = 0;

    private float $total = 0;

    /** @var ArrayCollection<int, OrderItem> $items */
    private ArrayCollection $items;

    private string $currency;

    private function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public static function create(int $id, string $currency): self
    {
        $self = new self();
        $self->id = $id;
        $self->status = OrderStatus::Created;
        $self->currency = $currency;

        return $self;
    }

    public function addItem(OrderItem $orderItem): void
    {
        $this->items->add($orderItem);
        $this->total += $orderItem->getTaxedAmount();
        $this->tax += $orderItem->getTax();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Order
     */
    public function setId(int $id): Order
    {
        $this->id = $id;
        return $this;
    }


    /**
     * @return OrderStatus
     */
    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    /**
     * @param OrderStatus $orderStatus
     * @return Order
     */
    public function setStatus(OrderStatus $orderStatus): Order
    {
        $this->status = $orderStatus;
        return $this;
    }

    /**
     * @return float
     */
    public function getTax(): float
    {
        return $this->tax;
    }

    /**
     * @param float $tax
     * @return Order
     */
    public function setTax(float $tax): Order
    {
        $this->tax = $tax;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * @param float $total
     * @return Order
     */
    public function setTotal(float $total): Order
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return ArrayCollection<int, OrderItem>
     */
    public function getItems(): ArrayCollection
    {
        return $this->items;
    }

    /**
     * @param ArrayCollection<int, OrderItem> $items
     * @return Order
     */
    public function setItems(ArrayCollection $items): Order
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return Order
     */
    public function setCurrency(string $currency): Order
    {
        $this->currency = $currency;
        return $this;
    }

    public function approve(): void
    {
        if ($this->status == OrderStatus::Shipped) {
            throw new ShippedOrdersCannotBeChangedException();
        }
        if ($this->status == OrderStatus::Rejected) {
            throw new RejectedOrderCannotBeApprovedException();
        }

        $this->status = OrderStatus::Approved;
    }

    public function reject(): void
    {
        if ($this->status == OrderStatus::Shipped) {
            throw new ShippedOrdersCannotBeChangedException();
        }
        if ($this->status == OrderStatus::Approved) {
            throw new ApprovedOrderCannotBeRejectedException();
        }
        $this->status = OrderStatus::Rejected;
    }

    public function ship(ShipmentService $shipmentService): void
    {
        if ($this->status == OrderStatus::Created || $this->status == OrderStatus::Rejected) {
            throw new OrderCannotBeShippedException();
        }

        if ($this->status == OrderStatus::Shipped) {
            throw new OrderCannotBeShippedTwiceException();
        }
        $this->status = OrderStatus::Shipped;
        $shipmentService->ship($this);
    }
}
