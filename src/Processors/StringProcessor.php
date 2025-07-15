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

namespace Chevere\VarDump\Processors;

use Chevere\Parameter\Interfaces\TypeInterface;
use Chevere\VarDump\Interfaces\ProcessorInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use Chevere\VarDump\Processors\Traits\ProcessorTrait;
use Throwable;

final class StringProcessor implements ProcessorInterface
{
    use ProcessorTrait;

    public const CONTROL_CHARS = [
        "\t" => '\t',
        "\n" => '\n',
        "\v" => '\v',
        "\f" => '\f',
        "\r" => '\r',
        "\033" => '\e',
    ];

    public const CONTROL_CHARS_RX = '/[\x00-\x1F\x7F]+/';

    private string $charset = '';

    private string $string = '';

    public function __construct(
        private VarDumperInterface $varDumper
    ) {
        $this->assertType();
        /** @var string $string */
        $string = $this->varDumper->dumpable()->var();
        $this->string = $string;
        $charset = ini_get('php.output_encoding')
            ?: ini_get('default_charset')
            ?: 'UTF-8'; // @codeCoverageIgnore
        $this->setCharset($charset);
        if (! preg_match('//u', $this->string)) {
            $this->handleBinaryString();
        }
        $this->info = 'length=' . mb_strlen($string);
    }

    public function type(): string
    {
        return TypeInterface::STRING;
    }

    public function write(): void
    {
        $this->varDumper->writer()->write(
            implode(' ', [
                $this->typeHighlighted(),
                $this->varDumper->format()
                    ->filterEncodedChars($this->string),
                $this->highlightParentheses($this->info),
            ])
        );
    }

    public function charset(): string
    {
        return $this->charset;
    }

    /**
     * Sets the default character encoding to use for non-UTF8 strings.
     */
    private function setCharset(string $charset): void
    {
        $charset = strtoupper($charset);
        $this->charset = ($charset === 'UTF-8' || $charset === 'UTF8')
            ? 'CP1252'
            : $charset;
    }

    private function handleBinaryString(): void
    {
        if (! function_exists('iconv')) {
            return; // @codeCoverageIgnore
        }
        $this->string = <<<STRING
        b"{$this->binaryDisplay($this->utf8Encode($this->string))}"
        STRING;
    }

    private function binaryDisplay(string $value): string
    {
        $map = static::CONTROL_CHARS;
        $startChar = '';
        $endChar = '';
        $value = preg_replace_callback(
            static::CONTROL_CHARS_RX,
            function ($c) use ($map, $startChar, $endChar) {
                $s = $startChar;
                $c = $c[$i = 0];
                do {
                    $s .= $map[$c[$i]] ?? sprintf('\x%02X', ord($c[$i]));
                } while (isset($c[++$i]));

                return $s . $endChar;
            },
            $value,
            -1,
            $charCount
        );

        return $value ?? '';
    }

    /**
     * Converts a non-UTF-8 string to UTF-8.
     */
    private function utf8Encode(string $string): string
    {
        try {
            $converted = iconv($this->charset, 'UTF-8', $string);
        } catch (Throwable) {
            $converted = false;
        }
        // @codeCoverageIgnoreStart
        if ($converted !== false) {
            return $converted;
        }

        try {
            $converted = iconv('CP1252', 'UTF-8', $string);
        } catch (Throwable) {
            $converted = false;
        }
        if ($converted !== false && $this->charset !== 'CP1252') {
            return $converted;
        }

        try {
            $converted = iconv('CP850', 'UTF-8', $string) ?: $string;
        } catch (Throwable) {
            $converted = $string;
        }

        return $converted;
        // @codeCoverageIgnoreEnd
    }
}
