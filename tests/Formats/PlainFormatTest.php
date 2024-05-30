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

use Chevere\VarDump\Formats\PlainFormat;
use Chevere\VarDump\Interfaces\HighlightInterface;
use PHPUnit\Framework\TestCase;

final class PlainFormatTest extends TestCase
{
    public function testIndent(): void
    {
        $indent = 5;
        $indented = (new PlainFormat())->indent($indent);
        $this->assertSame($indent, strlen($indented));
    }

    public function testEmphasis(): void
    {
        $string = 'string';
        $emphasized = (new PlainFormat())->emphasis($string);
        $this->assertSame($string, $emphasized);
    }

    public function testFilterEncodedChars(): void
    {
        $string = 'string</a>';
        $filtered = (new PlainFormat())->filterEncodedChars($string);
        $this->assertSame($string, $filtered);
    }

    public function testHighlight(): void
    {
        $string = 'string';
        $highlighted = (new PlainFormat())->highlight(HighlightInterface::KEYS[0], $string);
        $this->assertSame($string, $highlighted);
    }

    public function testDetails(): void
    {
        $this->assertSame('', (new PlainFormat())->detailsOpen());
        $this->assertSame('', (new PlainFormat())->detailsOpen(true));
        $this->assertSame('', (new PlainFormat())->detailsOpen(false));
        $this->assertSame('', (new PlainFormat())->detailsClose());
        $this->assertSame('', (new PlainFormat())->detailsPullUp());
    }
}
