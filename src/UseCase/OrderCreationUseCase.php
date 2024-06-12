<?php

declare(strict_types=1);

namespace Pitchart\TellDontAskKata\UseCase;

use Pitchart\TellDontAskKata\Domain\Order;
use Pitchart\TellDontAskKata\Domain\OrderItem;
use Pitchart\TellDontAskKata\Repository\OrderRepository;
use Pitchart\TellDontAskKata\Repository\ProductCatalog;

final class OrderCreationUseCase
{
    private OrderRepository $repository;

    private ProductCatalog $catalog;

    /**
     * @param OrderRepository $repository
     * @param ProductCatalog $catalog
     */
    public function __construct(OrderRepository $repository, ProductCatalog $catalog)
    {
        $this->repository = $repository;
        $this->catalog = $catalog;
    }

    /**
     * @throws UnknownProductException
     */
    public function run(SellItemsRequest $request): void
    {
        $order = Order::create(1, 'EUR');

        /** @var SellItemRequest $itemRequest */
        foreach ($request->items as $itemRequest) {
            $product = $this->catalog->getByName($itemRequest->productName);

            if ($product == null) {
                throw new UnknownProductException();
            }

            $order->addItem(
                OrderItem::of($product, $itemRequest->quantity),
            );


            $this->repository->save($order);
        }

    }
}
