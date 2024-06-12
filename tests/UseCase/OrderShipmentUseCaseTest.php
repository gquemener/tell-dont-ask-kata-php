<?php

declare(strict_types=1);

namespace Tests\Pitchart\TellDontAskKata\UseCase;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Pitchart\TellDontAskKata\Domain\Order;
use Pitchart\TellDontAskKata\Domain\OrderStatus;
use Pitchart\TellDontAskKata\UseCase\OrderCannotBeShippedException;
use Pitchart\TellDontAskKata\UseCase\OrderCannotBeShippedTwiceException;
use Pitchart\TellDontAskKata\UseCase\OrderShipmentRequest;
use Pitchart\TellDontAskKata\UseCase\OrderShipmentUseCase;
use Tests\Pitchart\TellDontAskKata\Doubles\InMemoryOrderRepository;
use Tests\Pitchart\TellDontAskKata\Doubles\NullShipmentService;
use Tests\Pitchart\TellDontAskKata\Doubles\TestShipmentService;

final class OrderShipmentUseCaseTest extends TestCase
{
    private InMemoryOrderRepository $orderRepository;

    private OrderShipmentUseCase $useCase;
    private TestShipmentService $shipmentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = new InMemoryOrderRepository();
        $this->shipmentService = new TestShipmentService();

        $this->useCase = new OrderShipmentUseCase($this->orderRepository, $this->shipmentService);
    }

    public function test_ship_approved_order(): void
    {
        $initialOrder = Order::create(1, 'EUR');
        $initialOrder->approve();

        $this->orderRepository->addOrder($initialOrder);

        $request = new OrderShipmentRequest(1);

        $this->useCase->run($request);

        $savedOrder = $this->orderRepository->getSavedOrder();
        Assert::assertNotNull($savedOrder);
        Assert::assertEquals(OrderStatus::Shipped, $savedOrder->getStatus());
        Assert::assertEquals($savedOrder, $this->shipmentService->getShippedOrder());
    }


    public function test_created_orders_can_not_be_shipped(): void
    {
        $initialOrder = Order::create(1, 'EUR');

        $this->orderRepository->addOrder($initialOrder);

        $request = new OrderShipmentRequest(1);

        $this->expectException(OrderCannotBeShippedException::class);

        try {
            $this->useCase->run($request);
        } finally {
            Assert::assertNull($this->orderRepository->getSavedOrder());
            Assert::assertNull($this->shipmentService->getShippedOrder());
        }
    }


    public function test_rejected_orders_can_not_be_shipped()
    {
        $initialOrder = Order::create(1, 'EUR');
        $initialOrder->reject();

        $this->orderRepository->addOrder($initialOrder);

        $request = new OrderShipmentRequest(1);

        $this->expectException(OrderCannotBeShippedException::class);

        try {
            $this->useCase->run($request);
        } finally {
            Assert::assertNull($this->orderRepository->getSavedOrder());
            Assert::assertNull($this->shipmentService->getShippedOrder());
        }
    }


    public function test_shipped_orders_can_not_be_shipped_again()
    {
        $initialOrder = Order::create(1, 'EUR');
        $initialOrder->approve();
        $initialOrder->ship(new NullShipmentService());

        $this->orderRepository->addOrder($initialOrder);

        $request = new OrderShipmentRequest(1);

        $this->expectException(OrderCannotBeShippedTwiceException::class);

        try {

            $this->useCase->run($request);
        } finally {
            Assert::assertNull($this->orderRepository->getSavedOrder());
            Assert::assertNull($this->shipmentService->getShippedOrder());
        }

    }

}
