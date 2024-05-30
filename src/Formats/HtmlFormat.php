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

final class HtmlFormat implements FormatInterface
{
    public const HTML_INLINE_PREFIX = '<span style="border-left: 1px solid rgba(108 108 108 / 35%);"></span>';

    public const HTML_EMPHASIS = '<em>%s</em>';

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

    public function detailsOpen(bool $open = false): string
    {
        $open = $open ? ' open' : '';

        return '<details style="line-height: normal; display: block; margin-top: -1.2em;"' . $open . '><summary style="line-height: 1em; height: 1.2em; left: -0.9em; position: relative;"></summary>';
    }

    public function detailsClose(): string
    {
        return '</details>';
    }

    public function detailsPullUp(): string
    {
        return '<div style="margin-top: -1.2em; height: 0;"></div>';
    }
}
