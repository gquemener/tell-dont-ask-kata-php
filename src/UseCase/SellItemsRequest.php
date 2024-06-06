<?php

declare(strict_types=1);

namespace Pitchart\TellDontAskKata\UseCase;

use Doctrine\Common\Collections\ArrayCollection;

final class SellItemsRequest
{
    /** @var ArrayCollection<int, SellItemRequest> */
    private ArrayCollection $items;

    /**
     * @return ArrayCollection<int, SellItemRequest>
     */
    public function getItems(): ArrayCollection
    {
        return $this->items;
    }

    /**
     * @param ArrayCollection<int, SellItemRequest> $items
     * @return SellItemsRequest
     */
    public function setItems(ArrayCollection $items): SellItemsRequest
    {
        $this->items = $items;
        return $this;
    }

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }


}
