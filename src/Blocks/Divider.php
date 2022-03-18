<?php

namespace Notion\Blocks;

use Notion\NotionException;
use Notion\Blocks\Exceptions\BlockTypeException;

/**
 * @psalm-import-type BlockJson from Block
 *
 * @psalm-type DividerJson = array{
 *      divider: array<empty, empty>
 * }
 * @psalm-immutable
 */
class Divider implements BlockInterface
{
    private const TYPE = Block::TYPE_DIVIDER;

    private Block $block;

    private function __construct(Block $block)
    {
        if (!$block->isDivider()) {
            throw new BlockTypeException(self::TYPE);
        }

        $this->block = $block;
    }

    public static function create(): self
    {
        $block = Block::create(self::TYPE);

        return new self($block);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockJson $array */
        $block = Block::fromArray($array);

        return new self($block);
    }

    public function toArray(): array
    {
        $array = $this->block->toArray();

        $array[self::TYPE] = [];

        return $array;
    }

    /** @internal */
    public function toUpdateArray(): array
    {
        return [
            self::TYPE => [],
            "archived" => $this->block()->archived(),
        ];
    }

    public function block(): Block
    {
        return $this->block;
    }

    public function changeChildren(array $children): self
    {
        throw new NotionException(
            "This block does not support children.",
            "no_children_support",
        );
    }

    public function archive(): BlockInterface
    {
        return new self(
            $this->block->archive(),
        );
    }
}
