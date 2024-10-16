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

namespace Chevere\Tests\Highlights;

use Chevere\Parameter\Interfaces\TypeInterface;
use Chevere\Tests\Traits\StripANSIColorsTrait;
use Chevere\VarDump\Highlights\ConsoleHighlight;
use Chevere\VarDump\Interfaces\HighlightInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use Colors\Color;
use OutOfRangeException;
use PHPUnit\Framework\TestCase;

final class ConsoleHighlightTest extends TestCase
{
    use StripANSIColorsTrait;

    public function testInvalidArgumentConstruct(): void
    {
        $this->expectException(OutOfRangeException::class);
        new ConsoleHighlight('invalid-argument');
    }

    public function testConstruct(): void
    {
        $dump = 'string';
        $open = '[38;5;';
        $close = '[0m';
        $expect = [
            TypeInterface::STRING => '%c0%m' . $dump,
            TypeInterface::FLOAT => '%c0%m' . $dump,
            TypeInterface::INT => '%c0%m' . $dump,
            TypeInterface::BOOL => '%c0%m' . $dump,
            TypeInterface::NULL => '%c0%m' . $dump,
            TypeInterface::OBJECT => '%c0%m' . $dump,
            TypeInterface::ARRAY => '%c0%m' . $dump,
            TypeInterface::RESOURCE => '%c0%m' . $dump,
            VarDumperInterface::FILE => '%c0%m' . $dump,
            VarDumperInterface::CLASS_REG => '%c0%m' . $dump,
            VarDumperInterface::OPERATOR => '%c0%m' . $dump,
            VarDumperInterface::FUNCTION => '%c0%m' . $dump,
            VarDumperInterface::MODIFIER => '%c0%m' . $dump,
            VarDumperInterface::VARIABLE => '%c0%m' . $dump,
            VarDumperInterface::EMPHASIS => '%c1%m' . $open . '%c0%m' . $dump . $close,
        ];
        $palette = ConsoleHighlight::palette();
        foreach ($expect as $k => &$v) {
            $paletteColor = $palette[$k];
            if (! is_array($paletteColor)) {
                $paletteColor = [$paletteColor];
            }
            foreach ($paletteColor as $pos => $colorCode) {
                $v = str_replace("%c{$pos}%", $colorCode, $v);
            }
            $v = $open . $v . $close;
        }
        $color = new Color();
        foreach (HighlightInterface::KEYS as $key) {
            $highlight = new ConsoleHighlight($key);
            $string = $highlight->highlight($dump);
            $expected = $expect[$key];
            if ($color->isSupported() === false) {
                $expected = $this->stripANSIColors($expected);
            }
            $this->assertSame($expected, $string);
        }
    }
}
