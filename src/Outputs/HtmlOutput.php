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

namespace Chevere\VarDump\Outputs;

use Chevere\VarDump\Interfaces\FormatInterface;

final class HtmlOutput extends Output
{
    public const CSS = <<<CSS
    @media (prefers-color-scheme: light) {
        :root {
            --textColor: #24292f;
            --backgroundColor: #f2f5f8;
            --inlineColor: #c4c5c7;
        }
    }
    @media (prefers-color-scheme: dark) {
        :root {
            --textColor: #ecf0f1;
            --backgroundColor: #132537;
            --inlineColor: #323e4a;
        }
    }
    html.chv-dump {
        background: var(--backgroundColor);
    }
    pre.chv-dump {
        font-size: 14px;
        font-family: 'Fira Code Retina', 'Operator Mono', Inconsolata, Menlo, Monaco, Consolas, monospace;
        line-height: normal;
        color: var(--textColor);
        padding: 1.25em;
        margin: 0.8em 0;
        word-break: break-word;
        white-space: pre-wrap;
        background: var(--backgroundColor);
        display: block;
        text-align: left;
        border: none;
        border-radius: 0.2857em;
    }
    .chv-dump-hr {
        opacity: 0.25;
    }
    .chv-dump-inline {
        border-left: 1px solid var(--inlineColor);

    }
    .chv-dump-details {
        line-height: normal;
        display: block;
        margin-top: -1.242857em;
    }
    * > .chv-dump-details:not(:last-child) {
        margin-bottom: -1.242857em;
    }
    .chv-dump-summary {
        height: 1.242857em;
        margin-left: -0.8em;
        position: relative;
    }
    .chv-dump-summary:hover {
        background: rgba(255, 255, 255, 0.1);
    }
    .chv-dump-summary::-webkit-details-marker {
        margin-top: 0.3em;
    }
    .chv-dump-float {
        color: #ff8700;
    }
    .chv-dump-int {
        color: #ff8700;
    }
    .chv-dump-string {
        color: #ff8700;
    }
    .chv-dump-bool {
        color: #ff8700;
    }
    .chv-dump-null {
        color: #ff8700;
    }
    .chv-dump-object {
        color: #fabb00;
    }
    .chv-dump-array {
        color: #27ae60;
    }
    .chv-dump-resource {
        color: #ff5f5f;
    }
    .chv-dump-file {
        color: #87afff;
    }
    .chv-dump-class {
        color: #fabb00;
    }
    .chv-dump-operator {
        color: #6c6c6c;
    }
    .chv-dump-function {
        color: #00afff;
    }
    .chv-dump-variable {
        color: #00afff;
    }
    .chv-dump-modifier {
        color: #d75fd7;
    }
    .chv-dump-emphasis {
        color: rgb(108 108 108 / 65%);
    }
    CSS;

    private bool $hasHeader = false;

    private static bool $isStyleWritten = false;

    public function tearDown(): void
    {
        $this->writer()->write('</pre>');
        if ($this->hasHeader) {
            $this->writer()->write('</body></html>');
        }
    }

    public function prepare(): void
    {
        // @infection-ignore-all
        if (! headers_sent() || headers_list() === []) {
            $this->hasHeader = true;
            $this->writer()->write(
                '<html class="chv-dump"><head><meta charset="UTF-8"></head><body>'
            );
        }
        if (! self::$isStyleWritten) {
            $this->writer()->write(
                '<style>' . preg_replace('/\s+/', ' ', self::CSS) . '</style>'
            );
            self::$isStyleWritten = true;
        }
        $this->writer()->write(
            '<pre class="chv-dump">'
            . $this->caller()
            . '<hr class="chv-dump-hr">'
        );
    }

    public function writeCallerFile(FormatInterface $format): void
    {
        $highlight = $this->getCallerFile($format);
        if ($highlight === '') {
            return;
        }
        $this->writer()->write(
            <<<HTML
            {$highlight}

            HTML
        );
    }
}
