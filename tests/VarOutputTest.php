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

namespace Chevere\Tests;

use Chevere\Tests\Traits\DebugBacktraceTrait;
use Chevere\VarDump\Formats\ConsoleFormat;
use Chevere\VarDump\Formats\HtmlFormat;
use Chevere\VarDump\Formats\PlainFormat;
use Chevere\VarDump\Outputs\ConsoleOutput;
use Chevere\VarDump\Outputs\HtmlOutput;
use Chevere\VarDump\Outputs\PlainOutput;
use Chevere\VarDump\VarOutput;
use Chevere\Writer\StreamWriter;
use PHPUnit\Framework\TestCase;
use function Chevere\Writer\streamTemp;

final class VarOutputTest extends TestCase
{
    use DebugBacktraceTrait;

    public function testPlainOutput(): void
    {
        $trace = $this->getDebugBacktrace();
        $writer = new StreamWriter(streamTemp(''));
        $varOutput = new VarOutput(
            writer: $writer,
            trace: $trace,
            format: new PlainFormat()
        );
        $output = new PlainOutput();
        $varOutput->process(
            $output,
            name: null,
            id: 123
        );
        $this->assertSame($trace, $output->trace());
        $this->assertSame(
            $this->getParsed($trace, 'output-plain'),
            $writer->__toString(),
        );
    }

    public function testConsoleOutput(): void
    {
        $trace = $this->getDebugBacktrace();
        $writer = new StreamWriter(streamTemp(''));
        $varOutput = new VarOutput(
            writer: $writer,
            trace: $trace,
            format: new ConsoleFormat(),
        );
        $varOutput->process(new ConsoleOutput(), name: null);
        $parsed = $this->getParsed($trace, 'output-console-color');
        $string = $writer->__toString();
        $parsed = $this->stripANSIColors($parsed);
        $string = $this->stripANSIColors($string);
        $this->assertSame($parsed, $string);
    }

    public function testHtmlOutput(): void
    {
        $trace = $this->getDebugBacktrace();
        $writer = new StreamWriter(streamTemp(''));
        $varOutput = new VarOutput(
            writer: $writer,
            trace: $trace,
            format: new HtmlFormat(),
        );
        $varOutput->process(new HtmlOutput(), name: null);
        $parsed = $this->getParsed($trace, 'output-html');
        $this->assertSame($parsed, $writer->__toString());
    }

    private function getParsed(array $trace, string $name): string
    {
        return strtr(include "src/{$name}.php", [
            '%handlerClassName%' => $trace[0]['class'],
            '%handlerFunctionName%' => $trace[0]['function'],
            '%fileLine%' => $trace[0]['file'] . ':' . $trace[0]['line'],
            '%className%' => $trace[1]['class'],
            '%functionName%' => $trace[1]['function'],
        ]);
    }

    private function stripANSIColors(string $string): string
    {
        return preg_replace('#\\x1b[[][^A-Za-z]*[A-Za-z]#', '', $string) ?? '';
    }
}
