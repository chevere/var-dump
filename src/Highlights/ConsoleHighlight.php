<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\VarDump\Highlights;

use Chevere\Parameter\Interfaces\TypeInterface;
use Chevere\VarDump\Highlights\Traits\AssertKeyTrait;
use Chevere\VarDump\Interfaces\HighlightInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use Colors\Color;
use Throwable;

final class ConsoleHighlight implements HighlightInterface
{
    use AssertKeyTrait;

    private Color $color;

    /**
     * @var array<string>
     */
    private array $style;

    public function __construct(string $key)
    {
        $this->assertKey($key);
        $this->color = new Color();
        // @infection-ignore-all
        $this->style = $this::palette()[$key] ?? ['reset'];
    }

    /**
     * @infection-ignore-all
     */
    public function highlight(string $dump): string
    {
        foreach ($this->style as $style) {
            try {
                $dump = $this->color->apply("color[{$style}]", $dump);
            } catch (Throwable) { // @codeCoverageIgnoreStart
                // Ignore if color not supported
            }
            // @codeCoverageIgnoreEnd
        }

        return $dump;
    }

    /**
     * @return array<string, array<string>>
     */
    public static function palette(): array
    {
        return [
            // DarkOrange
            TypeInterface::STRING => ['208'],
            TypeInterface::FLOAT => ['208'],
            TypeInterface::INT => ['208'],
            TypeInterface::BOOL => ['208'],
            TypeInterface::NULL => ['208'],
            // Gold1
            TypeInterface::OBJECT => ['220'],
            // Green3
            TypeInterface::ARRAY => ['41'],
            // IndianRed1
            TypeInterface::RESOURCE => ['203'],
            // SkyBlue2
            VarDumperInterface::FILE => ['111'],
            // light yellow
            VarDumperInterface::CLASS_REG => ['221'],
            // Grey42
            VarDumperInterface::OPERATOR => ['242'],
            // DeepSkyBlue1
            VarDumperInterface::FUNCTION => ['39'],
            VarDumperInterface::VARIABLE => ['39'],
            // Orchid
            VarDumperInterface::MODIFIER => ['170'],
            // dark gray italic
            VarDumperInterface::EMPHASIS => ['242', '3'],
        ];
    }
}
