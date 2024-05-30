<?php

declare(strict_types=1);

namespace GildedRose;

final class GildedRose
{
    /**
     * @var Item[]
     */
    private array $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /** 
    * Increase the quality of an item up to the max of 50.
    *
    * @param Item $item An item with name, quality, and sellIn.
    * @param int $iterations (optional) The number of quality increments --Default 1.
    */

    public function increaseQuality(Item $item, int $iterations = 1): void
    {
        $item->quality = min(50, $item->quality + $iterations);
    }

    /** 
    * Decrease the quality of an item down to the min of 0.
    *
    * @param Item $item An item with name, quality, and sellIn.
    */

    public function decreaseQuality(Item $item): void
    {
        $item->quality = max(0, $item->quality - 1);
    }

    /** 
    * Update the quality of backstage passes based on the sellIn value.
    *
    * @param Item $item An item with name, quality, and sellIn.
    */

    public function updateBackstagePasses(Item $item): void
    {
        if ($item->sellIn <= 0) {
            $item->quality = 0;
        } elseif ($item->sellIn <= 5) {
            $this->increaseQuality($item, 3);
        } elseif ($item->sellIn <= 10) {
            $this->increaseQuality($item, 2);
        } else {
            $this->increaseQuality($item);
        }
    }

    /** 
    * Update the quality of Aged Brie based on the sellIn date. 
    * Quality increases, with accelerated increase after the sellIn date.
    *
    * @param Item $item An item with name, quality, and sellIn.
    */

    public function updateAgedBrie(Item $item): void
    {
        if ($item->sellIn <= 0) {
            $this->increaseQuality($item, 2);
        } else {
            $this->increaseQuality($item);
        }
    }

    /** 
    * Update the quality of the item based on their name and/or sellIn value.
    *
    * @param Item $item An item with name, quality, and sellIn.
    */

    public function updateItemQuality(Item $item): void
    {   
        if ($item->name === 'Sulfuras, Hand of Ragnaros') { // "Legendary Item" --Values do not change.
            return;
        }

        if ($item->name === 'Backstage passes to a TAFKAL80ETC concert') {
            $this->updateBackstagePasses($item);
        } elseif ($item->name === 'Aged Brie') {
            $this->updateAgedBrie($item);
        } else {
            $this->decreaseQuality($item);
            if ($item->sellIn <= 0 || $item->name === 'Conjured Mana Cake') {
                $this->decreaseQuality($item);
            }
        }

        $item->sellIn--;
    }

    /** 
    * Loop through all items and update their quality and sellIn values.
    */

    public function updateQuality(): void
    {
        foreach ($this->items as $item) {
            $this->updateItemQuality($item);
        }
    }
}
