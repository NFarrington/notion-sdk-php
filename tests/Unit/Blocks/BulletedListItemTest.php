<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\BulletedListItem;
use Notion\Blocks\Exceptions\BlockTypeException;
use Notion\Common\Date;
use Notion\Common\RichText;
use PHPUnit\Framework\TestCase;

class BulletedListItemTest extends TestCase
{
    public function test_create_empty_item(): void
    {
        $item = BulletedListItem::create();

        $this->assertEmpty($item->text());
        $this->assertEmpty($item->children());
    }

    public function test_create_from_string(): void
    {
        $item = BulletedListItem::fromString("Dummy item.");

        $this->assertEquals("Dummy item.", $item->toString());
    }

    public function test_create_from_array(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "bulleted_list_item",
            "bulleted_list_item"        => [
                "rich_text" => [
                    [
                        "plain_text"  => "Notion items ",
                        "href"        => null,
                        "type"        => "rich_text",
                        "rich_text"        => [
                            "content" => "Notion items ",
                        ],
                        "annotations" => [
                            "bold"          => false,
                            "italic"        => false,
                            "strikethrough" => false,
                            "underline"     => false,
                            "code"          => false,
                            "color"         => "default",
                        ],
                    ],
                    [
                        "plain_text"  => "rock!",
                        "href"        => null,
                        "type"        => "rich_text",
                        "rich_text"        => [
                            "content" => "rock!",
                        ],
                        "annotations" => [
                            "bold"          => true,
                            "italic"        => false,
                            "strikethrough" => false,
                            "underline"     => false,
                            "code"          => false,
                            "color"         => "red",
                        ],
                    ],
                ],
                "children" => [],
            ],
        ];

        $item = BulletedListItem::fromArray($array);

        $this->assertCount(2, $item->text());
        $this->assertEmpty($item->children());
        $this->assertEquals("Notion items rock!", $item->toString());
        $this->assertFalse($item->block()->archived());

        $this->assertEquals($item, BlockFactory::fromArray($array));
    }

    public function test_error_on_wrong_type(): void
    {
        $this->expectException(BlockTypeException::class);
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "wrong-type",
            "bulleted_list_item"        => [
                "rich_text"     => [],
                "children" => [],
            ],
        ];

        BulletedListItem::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $i = BulletedListItem::fromString("Simple item");

        $expected = [
            "object"           => "block",
            "created_time"     => $i->block()->createdTime()->format(Date::FORMAT),
            "last_edited_time" => $i->block()->lastEditedType()->format(Date::FORMAT),
            "archived"         => false,
            "has_children"     => false,
            "type"             => "bulleted_list_item",
            "bulleted_list_item" => [
                "rich_text" => [[
                    "plain_text"  => "Simple item",
                    "href"        => null,
                    "type"        => "text",
                    "text"        => [
                        "content" => "Simple item",
                    ],
                    "annotations" => [
                        "bold"          => false,
                        "italic"        => false,
                        "strikethrough" => false,
                        "underline"     => false,
                        "code"          => false,
                        "color"         => "default",
                    ],
                ]],
                "children" => [],
            ],
        ];

        $this->assertEquals($expected, $i->toArray());
    }

    public function test_replace_text(): void
    {
        $oldItem = BulletedListItem::fromString("This is an old item");

        $newItem = $oldItem->withText([
            RichText::createText("This is a "),
            RichText::createText("new item"),
        ]);

        $this->assertEquals("This is an old item", $oldItem->toString());
        $this->assertEquals("This is a new item", $newItem->toString());
    }

    public function test_append_text(): void
    {
        $oldItem = BulletedListItem::fromString("A item");

        $newItem = $oldItem->appendText(
            RichText::createText(" can be extended.")
        );

        $this->assertEquals("A item", $oldItem->toString());
        $this->assertEquals("A item can be extended.", $newItem->toString());
    }

    public function test_replace_children(): void
    {
        $nested1 = BulletedListItem::fromString("Nested item 1");
        $nested2 = BulletedListItem::fromString("Nested item 2");
        $item = BulletedListItem::fromString("Simple item.")->changeChildren([ $nested1, $nested2 ]);

        $this->assertCount(2, $item->children());
        $this->assertEquals($nested1, $item->children()[0]);
        $this->assertEquals($nested2, $item->children()[1]);
    }

    public function test_append_child(): void
    {
        $item = BulletedListItem::fromString("Simple item.");
        $nested = BulletedListItem::fromString("Nested item");
        $item = $item->appendChild($nested);

        $this->assertCount(1, $item->children());
        $this->assertEquals($nested, $item->children()[0]);
    }

    public function test_array_for_update_operations(): void
    {
        $block = BulletedListItem::create();

        $array = $block->toUpdateArray();

        $this->assertCount(2, $array);
    }
}
