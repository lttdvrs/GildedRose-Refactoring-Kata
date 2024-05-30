<?php

declare(strict_types=1);

namespace Tests;

use GildedRose\GildedRose;
use GildedRose\Item;
use PHPUnit\Framework\TestCase;

class GildedRoseTest extends TestCase
{
    public function testFoo(): void
    {
        $items = [new Item('foo', 0, 0)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame('foo', $items[0]->name);
    }

    public function testQualityDecreaseTwiceAfterSellInDatePassed(): void
    {
        $items = [
            new Item('Sulfuras, Hand of Ragnaros', 0, 10), // Quality never changes.
            new Item('Aged Brie', 0, 4), // Quality increases instead of decreasing
            new Item('Elixir of the Mongoose', 0, 2), // Quility decreases by two
        ];

        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(
            [10, 6, 0],
            array_map(fn ($i) => $i -> quality, $items) // Map through items and their quality
        );
    }

    public function testItemQualityIsNeverNegative(): void
    {
        $items = [new Item('Elixir of the Mongoose', -1, 0)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(0, $items[0]->quality);
        $this->assertSame(-2, $items[0]->sellIn);
    }

    public function testAgedBrieIncreasesInQuality(): void
    {
        $items = [new Item('Aged Brie', 4, 2)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(3, $items[0]->quality);
        $this->assertSame(3, $items[0]->sellIn);
    }

    public function testItemQualityIsNotHigherThanFifty(): void
    {
        $items = [new Item('Aged Brie', 4, 50)]; // Test Quality on Brie since the Quality increases.
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(50, $items[0]->quality);
        $this->assertSame(3, $items[0]->sellIn);
    }

    public function testTwoItemQualityIsNotHigherThanFifty(): void
    {
        $items = [new Item('Aged Brie', 4, 49)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(50, $items[0]->quality);
        $this->assertSame(3, $items[0]->sellIn);
    }

    public function testSulfurasDoesNotDecreasesInQuality(): void
    {
        $items = [new Item('Sulfuras, Hand of Ragnaros', 4, 11)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(11, $items[0]->quality);
        $this->assertSame(4, $items[0]->sellIn);
    }

    public function testBackstagePassesQualityIncreaseWhenTenDaysNearSellIn(): void
    {
        // Test if the quality increases by two after the sellIn value is <= 10.

        $items = [new Item('Backstage passes to a TAFKAL80ETC concert', 10, 10)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(12, $items[0]->quality);
        $this->assertSame(9, $items[0]->sellIn);
    }

    public function testBackstagePassesQualityIncreaseWhenFiveNearSellIn(): void
    {
        // Test if the quality increases by three after the sellIn value is <= 5.

        $items = [new Item('Backstage passes to a TAFKAL80ETC concert', 5, 10)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(13, $items[0]->quality);
        $this->assertSame(4, $items[0]->sellIn);
    }

    public function testBackstagePassesQualityGoesDownAfterSellInDue(): void
    {
        $items = [new Item('Backstage passes to a TAFKAL80ETC concert', 0, 10)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(0, $items[0]->quality);
        $this->assertSame(-1, $items[0]->sellIn);
    }

    public function testConjuredQualityDecreasesTwiceFaster(): void
    {
        $items = [new Item('Conjured Mana Cake', 0, 10)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame(8, $items[0]->quality);
        $this->assertSame(-1, $items[0]->sellIn);
    }
}
