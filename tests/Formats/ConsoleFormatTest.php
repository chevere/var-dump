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

namespace Chevere\Tests\Formats;

use Chevere\VarDump\Formats\ConsoleFormat;
use Chevere\VarDump\Interfaces\HighlightInterface;
use PHPUnit\Framework\TestCase;

final class ConsoleFormatTest extends TestCase
{
    public function testIndent(): void
    {
        $indent = 5;
        $indented = (new ConsoleFormat())->indent($indent);
        $this->assertSame($indent, strlen($indented));
    }

    public function testEmphasis(): void
    {
        $string = 'string';
        $emphasized = (new ConsoleFormat())->emphasis($string);
        $this->assertTrue(strlen($emphasized) >= strlen($string));
    }

    public function testFilterEncodedChars(): void
    {
        $string = 'string</a>';
        $filtered = (new ConsoleFormat())->filterEncodedChars($string);
        $this->assertSame($string, $filtered);
    }

    public function testHighlight(): void
    {
        $string = 'string';
        $highlighted = (new ConsoleFormat())
            ->highlight(HighlightInterface::KEYS[0], $string);
        $this->assertTrue(strlen($highlighted) >= strlen($string));
    }

    public function testDetails(): void
    {
        $this->assertSame('', (new ConsoleFormat())->detailsOpen());
        $this->assertSame('', (new ConsoleFormat())->detailsOpen(true));
        $this->assertSame('', (new ConsoleFormat())->detailsOpen(false));
        $this->assertSame('', (new ConsoleFormat())->detailsClose());
        $this->assertSame('', (new ConsoleFormat())->detailsPullUp());
    }
}
