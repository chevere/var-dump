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

use Chevere\Type\Interfaces\TypeInterface;
use Chevere\VarDump\Interfaces\ProcessorInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use Chevere\VarDump\Processors\Traits\ProcessorTrait;

final class StringProcessor implements ProcessorInterface
{
    use ProcessorTrait;

    private string $charset = '';

    private string $string = '';

    public function __construct(
        private VarDumperInterface $varDumper
    ) {
        $this->assertType();
        /** @var string $string */
        $string = $this->varDumper->dumpable()->var();
        $this->string = $string;
        $this->setCharset(
            ini_get('php.output_encoding')
            ?: ini_get('default_charset')
            ?: 'UTF-8'
        );
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
                    ->getFilterEncodedChars($this->string),
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
        $this->charset = $charset === 'UTF-8' || $charset === 'UTF8'
            ? 'CP1252' : $charset;
    }

    private function handleBinaryString(): void
    {
        if (! function_exists('iconv')) {
            return; // @codeCoverageIgnore
        }
        $this->string = <<<STRING
        b"{$this->utf8Encode($this->string)}"
        STRING;
    }

    /**
     * Converts a non-UTF-8 string to UTF-8.
     */
    private function utf8Encode(string $string): string
    {
        $converted = iconv($this->charset, 'UTF-8', $string);
        if ($converted !== false) {
            return $converted;
        }
        // @codeCoverageIgnoreStart
        $converted = iconv('CP1252', 'UTF-8', $string);
        if ($converted !== false && $this->charset !== 'CP1252') {
            return $converted;
        }

        return iconv('CP850', 'UTF-8', $string) ?: $string;
        // @codeCoverageIgnoreEnd
    }
}
