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
    /**
     * @dataProvider provideIndent
     */
    public function testIndent(int $indent): void
    {
        $prefix = HtmlFormat::HTML_INLINE_PREFIX;
        $indented = (new HtmlFormat())->indent($indent);
        $stripped = strip_tags($indented);
        $expected = str_repeat('  ', $indent);
        $this->assertSame($expected, $stripped);
    }

    public function provideIndent(): array
    {
        return [
            [0],
            [10],
            [100],
        ];
    }

    public function testEmphasis(): void
    {
        $string = 'string';
        $emphasized = (new HtmlFormat())->emphasis($string);
        $this->assertTrue(strlen($emphasized) > strlen($string));
    }

    public function testFilterEncodedChars(): void
    {
        $string = 'string</a>';
        $filtered = (new HtmlFormat())->filterEncodedChars($string);
        $this->assertTrue(strlen($filtered) > strlen($string));
    }

    public function testHighlight(): void
    {
        $string = 'string';
        $highlighted = (new HtmlFormat())
            ->highlight(HighlightInterface::KEYS[0], $string);
        $this->assertTrue(strlen($highlighted) > strlen($string));
    }

    public function testDetailsOpen(): void
    {
        $this->assertStringContainsString(
            '<details ',
            (new HtmlFormat())->detailsOpen(true)
        );
        $this->assertStringContainsString(
            ' open>',
            (new HtmlFormat())->detailsOpen(true)
        );
    }

    public function testDetailsClose(): void
    {
        $this->assertSame(
            '</details>',
            (new HtmlFormat())->detailsClose()
        );
    }

    public function testDetailsPullUp(): void
    {
        $needle = '<div ';
        $details = (new HtmlFormat())->detailsPullUp();
        $this->assertStringContainsString($needle, $details);
    }
}
