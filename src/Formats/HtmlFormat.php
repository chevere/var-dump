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

namespace Chevere\VarDump\Formats;

use Chevere\VarDump\Highlights\HtmlHighlight;
use Chevere\VarDump\Interfaces\FormatInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;

class HtmlFormat implements FormatInterface
{
    public const HTML_INLINE_PREFIX = '<span class="chv-dump-inline"></span>';

    public const HTML_EMPHASIS = '<em>%s</em>';

    public const HTML_DETAILS_OPEN = '<details class="chv-dump-details"%s>';

    public const HTML_DETAILS_CLOSE = '</details>';

    public const HTML_SUMMARY = '<summary class="chv-dump-summary"></summary>';

    public function indent(int $indent): string
    {
        // @infection-ignore-all
        return str_repeat(' ' . self::HTML_INLINE_PREFIX . ' ', $indent);
    }

    public function emphasis(string $string): string
    {
        return sprintf(
            self::HTML_EMPHASIS,
            (new HtmlHighlight(VarDumperInterface::EMPHASIS))
                ->highlight($string)
        );
    }

    public function filterEncodedChars(string $string): string
    {
        return htmlspecialchars($string);
    }

    public function highlight(string $key, string $string): string
    {
        return (new HtmlHighlight($key))->highlight($string);
    }

    public function detailsOpen(bool $open): string
    {
        return sprintf(self::HTML_DETAILS_OPEN, $open ? ' open' : '') . self::HTML_SUMMARY;
    }

    public function detailsClose(): string
    {
        return self::HTML_DETAILS_CLOSE;
    }
}
