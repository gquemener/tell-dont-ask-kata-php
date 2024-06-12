<?php

declare(strict_types=1);

namespace Pitchart\TellDontAskKata\UseCase;

use Pitchart\TellDontAskKata\Repository\OrderRepository;

final class OrderApprovalUseCase
{
    private OrderRepository $repository;

    /**
     * @param OrderRepository $repository
     */
    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws RejectedOrderCannotBeApprovedException
     * @throws ApprovedOrderCannotBeRejectedException
     * @throws ShippedOrdersCannotBeChangedException
     */
    public function run(OrderApprovalRequest $request): void
    {
        $order = $this->repository->getById($request->id);

        if ($request->approved) {
            $order->approve();
        } else {
            $order->reject();
        }

        $this->repository->save($order);
    }


}
