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

use Chevere\Str\Str;
use Chevere\Tests\Traits\DebugBacktraceTrait;
use Chevere\VarDump\Formats\ConsoleFormat;
use Chevere\VarDump\Formats\HtmlFormat;
use Chevere\VarDump\Formats\PlainFormat;
use Chevere\VarDump\Outputs\ConsoleOutput;
use Chevere\VarDump\Outputs\HtmlOutput;
use Chevere\VarDump\Outputs\PlainOutput;
use Chevere\VarDump\VarOutput;
use function Chevere\Writer\streamTemp;
use Chevere\Writer\StreamWriter;
use PHPUnit\Framework\TestCase;

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
        $varOutput->process(
            new PlainOutput(),
            name: null,
            id: 123
        );
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
        $parsed = (new Str($parsed))->withStripANSIColors()->__toString();
        $string = (new Str($string))->withStripANSIColors()->__toString();
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
        return strtr(include "_resources/${name}.php", [
            '%handlerClassName%' => $trace[0]['class'],
            '%handlerFunctionName%' => $trace[0]['function'],
            '%fileLine%' => $trace[0]['file'] . ':' . $trace[0]['line'],
            '%className%' => $trace[1]['class'],
            '%functionName%' => $trace[1]['function'],
        ]);
    }
}
