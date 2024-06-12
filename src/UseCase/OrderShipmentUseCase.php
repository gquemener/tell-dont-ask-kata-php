<?php

declare(strict_types=1);

namespace Pitchart\TellDontAskKata\UseCase;

use Pitchart\TellDontAskKata\Repository\OrderRepository;
use Pitchart\TellDontAskKata\Services\ShipmentService;

final class OrderShipmentUseCase
{
    private OrderRepository $repository;
    private ShipmentService $shipmentService;

    /**
     * @param OrderRepository $repository
     * @param ShipmentService $shipmentService
     */
    public function __construct(OrderRepository $repository, ShipmentService $shipmentService)
    {
        $this->repository = $repository;
        $this->shipmentService = $shipmentService;
    }

    /**
     * @throws OrderCannotBeShippedTwiceException
     * @throws OrderCannotBeShippedException
     */
    public function run(OrderShipmentRequest $request): void
    {
        $order = $this->repository->getById($request->id);

        $order->ship($this->shipmentService);

        $this->repository->save($order);
    }
}
