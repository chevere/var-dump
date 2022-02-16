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

use Chevere\VarDump\Formats\VarDumpPlainFormat;
use Chevere\VarDump\Interfaces\VarDumpHighlightInterface;
use PHPUnit\Framework\TestCase;

final class VarDumpPlainFormatTest extends TestCase
{
    public function testIndent(): void
    {
        $indent = 5;
        $indented = (new VarDumpPlainFormat())->getIndent($indent);
        $this->assertSame($indent, strlen($indented));
    }

    public function testEmphasis(): void
    {
        $string = 'string';
        $emphasized = (new VarDumpPlainFormat())->getEmphasis($string);
        $this->assertSame($string, $emphasized);
    }

    public function testFilterEncodedChars(): void
    {
        $string = 'string</a>';
        $filtered = (new VarDumpPlainFormat())->getFilterEncodedChars($string);
        $this->assertSame($string, $filtered);
    }

    public function testHighlight(): void
    {
        $string = 'string';
        $highlighted = (new VarDumpPlainFormat())->getHighlight(VarDumpHighlightInterface::KEYS[0], $string);
        $this->assertSame($string, $highlighted);
    }
}
