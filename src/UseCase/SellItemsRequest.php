<?php

declare(strict_types=1);

namespace Pitchart\TellDontAskKata\UseCase;

use Doctrine\Common\Collections\ArrayCollection;

final readonly class SellItemsRequest
{
    public ArrayCollection $items;

    public function __construct(SellItemRequest ...$items)
    {
        $this->items = new ArrayCollection($items);
    }
}
