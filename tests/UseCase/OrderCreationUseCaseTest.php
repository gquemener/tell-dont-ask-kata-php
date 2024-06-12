<?php

declare(strict_types=1);

namespace Tests\Pitchart\TellDontAskKata\UseCase;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Pitchart\TellDontAskKata\Domain\Category;
use Pitchart\TellDontAskKata\Domain\OrderStatus;
use Pitchart\TellDontAskKata\Domain\Product;
use Pitchart\TellDontAskKata\UseCase\OrderCreationUseCase;
use Pitchart\TellDontAskKata\UseCase\SellItemRequest;
use Pitchart\TellDontAskKata\UseCase\SellItemsRequest;
use Pitchart\TellDontAskKata\UseCase\UnknownProductException;
use Tests\Pitchart\TellDontAskKata\Doubles\InMemoryOrderRepository;
use Tests\Pitchart\TellDontAskKata\Doubles\InMemoryProductCatalog;

final class OrderCreationUseCaseTest extends TestCase
{
    private OrderCreationUseCase $useCase;
    private InMemoryProductCatalog $productCatalog;
    private InMemoryOrderRepository $orderRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $food = (new Category())->setName('food')->setTaxPercentage(10);

        $this->productCatalog = new InMemoryProductCatalog(new ArrayCollection([
            (new Product())->setName("salad")->setPrice(3.56)->setCategory($food),
            (new Product())->setName("tomato")->setPrice(4.65)->setCategory($food),
        ]));

        $this->orderRepository = new InMemoryOrderRepository();
        $this->useCase = new OrderCreationUseCase($this->orderRepository, $this->productCatalog);
    }


    public function test_multiple_items(): void
    {
        $request = new SellItemsRequest(
            new SellItemRequest('salad', 2),
            new SellItemRequest('tomato', 3),
        );

        $this->useCase->run($request);

        $insertedOrder = $this->orderRepository->getSavedOrder();
        Assert::assertEquals(OrderStatus::Created, $insertedOrder->getStatus());
        Assert::assertEquals(23.20, $insertedOrder->getTotal());
        Assert::assertEquals(2.13, $insertedOrder->getTax());
        Assert::assertEquals("EUR", $insertedOrder->getCurrency());
        Assert::assertEquals(2, $insertedOrder->getItems()->count());
        Assert::assertEquals("salad", $insertedOrder->getItems()[0]->getProduct()->getName());
        Assert::assertEquals(3.56, $insertedOrder->getItems()[0]->getProduct()->getPrice());
        Assert::assertEquals(2, $insertedOrder->getItems()[0]->getQuantity());
        Assert::assertEquals(7.84, $insertedOrder->getItems()[0]->getTaxedAmount());
        Assert::assertEquals(0.72, $insertedOrder->getItems()[0]->getTax());
        Assert::assertEquals("tomato", $insertedOrder->getItems()[1]->getProduct()->getName());
        Assert::assertEquals(4.65, $insertedOrder->getItems()[1]->getProduct()->getPrice());
        Assert::assertEquals(3, $insertedOrder->getItems()[1]->getQuantity());
        Assert::assertEquals(15.36, $insertedOrder->getItems()[1]->getTaxedAmount());
        Assert::assertEquals(1.41, $insertedOrder->getItems()[1]->getTax());
    }

    public function test_unknown_product(): void
    {
        $request = new SellItemsRequest(
            new SellItemRequest('unknown product', 10),
        );

        $this->expectException(UnknownProductException::class);

        $this->useCase->run($request);
    }
}
