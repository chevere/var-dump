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
    html.chv-dump {
        background: #132537;
    }
    pre.chv-dump {
        font-size: 14px;
        font-family: 'Fira Code Retina', 'Operator Mono', Inconsolata, Menlo, Monaco, Consolas, monospace;
        line-height: normal;
        color: #ecf0f1;
        padding: 1.25em;
        margin: 0.8em 0;
        word-break: break-word;
        white-space: pre-wrap;
        background:  #132537;
        display: block;
        text-align: left;
        border: none;
        border-radius: 0.2857em;
    }
    .chv-dump-hr {
        opacity: 0.25;
    }
    .chv-dump-inline {
        border-left: 1px solid rgba(108 108 108 / 35%);
    }
    .chv-dump-details {
        line-height: normal;
        display: block;
        margin-top: -1.2em;
    }
    pre.chv-dump > details:not(:last-child) {
        margin-bottom: -1.2em;
    }
    .chv-dump-summary {
        height: 1.2em;
        margin-left: -0.8em;
        position: relative;
    }
    .chv-dump-summary:hover {
        background: rgba(255, 255, 255, 0.1);
    }
    .chv-dump-summary::-webkit-details-marker {
        margin-top: 0.3em;
    }
    .chv-details-pull-up {
        margin-top: -1.2em;
        height: 0;
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
    .chv-dump-class-reg {
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

    private static $isStyleWritten = false;

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
