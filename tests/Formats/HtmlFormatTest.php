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

use Chevere\VarDump\Formats\HtmlFormat;
use Chevere\VarDump\Interfaces\HighlightInterface;
use PHPUnit\Framework\TestCase;

final class HtmlFormatTest extends TestCase
{
    public function testIndent(): void
    {
        $baseIndent = strip_tags(HtmlFormat::HTML_INLINE_PREFIX);
        $indent = 5;
        $indented = (new HtmlFormat())->getIndent($indent);
        $stripped = strip_tags($indented);
        $expected = str_repeat($baseIndent, $indent);
        $this->assertSame($expected, $stripped);
    }

    public function testEmphasis(): void
    {
        $string = 'string';
        $emphasized = (new HtmlFormat())->getEmphasis($string);
        $this->assertTrue(strlen($emphasized) > strlen($string));
    }

    public function testFilterEncodedChars(): void
    {
        $string = 'string</a>';
        $filtered = (new HtmlFormat())->getFilterEncodedChars($string);
        $this->assertTrue(strlen($filtered) > strlen($string));
    }

    public function testHighlight(): void
    {
        $string = 'string';
        $highlighted = (new HtmlFormat())->getHighlight(HighlightInterface::KEYS[0], $string);
        $this->assertTrue(strlen($highlighted) > strlen($string));
    }
}