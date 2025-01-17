<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\Callout;
use Notion\Blocks\Exceptions\BlockTypeException;
use Notion\Common\Date;
use Notion\Common\Emoji;
use Notion\Common\RichText;
use PHPUnit\Framework\TestCase;

class CalloutTest extends TestCase
{
    public function test_create_empty_callout(): void
    {
        $callout = Callout::create();

        $this->assertEmpty($callout->text());
        $this->assertEmpty($callout->children());
    }

    public function test_create_from_string(): void
    {
        $callout = Callout::fromString("☀️", "Dummy callout.");

        $this->assertEquals("Dummy callout.", $callout->toString());
    }

    public function test_create_from_array_with_emoji_icon(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "callout",
            "callout"        => [
                "rich_text" => [
                    [
                        "plain_text"  => "Notion callouts ",
                        "href"        => null,
                        "type"        => "rich_text",
                        "rich_text"        => [
                            "content" => "Notion callouts ",
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
                "icon" => [
                    "type"  => "emoji",
                    "emoji" => "☀️",
                ],
                "children" => [],
            ],
        ];

        $callout = Callout::fromArray($array);

        $this->assertCount(2, $callout->text());
        $this->assertEmpty($callout->children());
        $this->assertEquals("Notion callouts rock!", $callout->toString());
        if ($callout->iconIsEmoji()) {
            $this->assertEquals("☀️", $callout->icon()->emoji());
        }
        $this->assertFalse($callout->block()->archived());

        $this->assertEquals($callout, BlockFactory::fromArray($array));
    }

    public function test_create_from_array_with_icon_file(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "callout",
            "callout"        => [
                "rich_text" => [
                    [
                        "plain_text"  => "Notion callouts ",
                        "href"        => null,
                        "type"        => "rich_text",
                        "rich_text"        => [
                            "content" => "Notion callouts ",
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
                "icon" => [
                    "type"  => "external",
                    "external"  => [
                        "type" => "external",
                        "url"  => "https://imgur.com/gallery/Iy8yE5h",
                    ],
                ],
                "children" => [],
            ],
        ];

        $callout = Callout::fromArray($array);

        $this->assertCount(2, $callout->text());
        $this->assertEmpty($callout->children());
        $this->assertEquals("Notion callouts rock!", $callout->toString());
        $this->assertFalse($callout->block()->archived());
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
            "callout"        => [
                "rich_text"     => [],
                "children" => [],
                "icon"     => [
                    "type"  => "emoji",
                    "emoji" => "☀️",
                ],
            ],
        ];

        Callout::fromArray($array);
    }

    public function test_error_on_wrong_icon_type(): void
    {
        $this->expectException(\Exception::class);
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "callout",
            "callout"        => [
                "rich_text"     => [],
                "children" => [],
                "icon"     => [ "type"  => "wrong-type"],
            ],
        ];

        Callout::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $c = Callout::fromString("☀️", "Simple callout");

        $expected = [
            "object"           => "block",
            "created_time"     => $c->block()->createdTime()->format(Date::FORMAT),
            "last_edited_time" => $c->block()->lastEditedType()->format(Date::FORMAT),
            "archived"         => false,
            "has_children"      => false,
            "type"             => "callout",
            "callout"        => [
                "rich_text" => [[
                    "plain_text"  => "Simple callout",
                    "href"        => null,
                    "type"        => "text",
                    "text"        => [
                        "content" => "Simple callout",
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
                "icon" => [
                    "type"  => "emoji",
                    "emoji" => "☀️",
                ],
                "children" => [],
            ],
        ];

        $this->assertEquals($expected, $c->toArray());
    }

    public function test_replace_text(): void
    {
        $oldCallout = Callout::fromString("☀️", "This is an old callout");

        $newCallout = $oldCallout->withText([
            RichText::createText("This is a "),
            RichText::createText("new callout"),
        ]);

        $this->assertEquals("This is an old callout", $oldCallout->toString());
        $this->assertEquals("This is a new callout", $newCallout->toString());
    }

    public function test_append_text(): void
    {
        $oldCallout = Callout::fromString("☀️", "A callout");

        $newCallout = $oldCallout->appendText(
            RichText::createText(" can be extended.")
        );

        $this->assertEquals("A callout", $oldCallout->toString());
        $this->assertEquals("A callout can be extended.", $newCallout->toString());
    }

    public function test_replace_children(): void
    {
        $nested1 = Callout::fromString("☀️", "Nested callout 1");
        $nested2 = Callout::fromString("☀️", "Nested callout 2");
        $callout = Callout::fromString("☀️", "Simple callout.")->changeChildren([$nested1, $nested2]);

        $this->assertCount(2, $callout->children());
        $this->assertEquals($nested1, $callout->children()[0]);
        $this->assertEquals($nested2, $callout->children()[1]);
    }

    public function test_append_child(): void
    {
        $callout = Callout::fromString("☀️", "Simple callout.");
        $nested = Callout::fromString("☀️", "Nested callout");
        $callout = $callout->appendChild($nested);

        $this->assertCount(1, $callout->children());
        $this->assertEquals($nested, $callout->children()[0]);
    }

    public function test_replace_icon(): void
    {
        $callout = Callout::fromString("☀️", "Simple callout.")
            ->withIcon(Emoji::create("🌙"));

        if ($callout->iconIsEmoji()) {
            $this->assertEquals("🌙", $callout->icon()->emoji());
        }
    }

    public function test_array_for_update_operations(): void
    {
        $block = Callout::create();

        $array = $block->toUpdateArray();

        $this->assertCount(2, $array);
    }
}
