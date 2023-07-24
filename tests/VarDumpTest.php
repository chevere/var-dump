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

use Chevere\VarDump\Formats\PlainFormat;
use Chevere\VarDump\Interfaces\VarDumpInterface;
use Chevere\VarDump\Outputs\PlainOutput;
use Chevere\VarDump\VarDump;
use function Chevere\Writer\streamTemp;
use Chevere\Writer\StreamWriter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use stdClass;

final class VarDumpTest extends TestCase
{
    public function testConstruct(): void
    {
        $varDump = $this->getVarDump();
        $this->assertSame(0, $varDump->shift());
        $this->assertSame([], $varDump->variables());
    }

    public function testWithVariables(): void
    {
        $stream = $this->getStream();
        $writer = new StreamWriter($stream);
        $variable = new stdClass();
        $varDump = $this->getVarDump();
        $varDumpWithVariables = $varDump->withVariables($variable);
        $this->assertNotSame($varDump, $varDumpWithVariables);
        $this->assertEqualsCanonicalizing(
            [$variable],
            $varDumpWithVariables->variables()
        );
        $varDumpWithVariables->process($writer);
        $line = strval(__LINE__ - 1);
        $hrLine = str_repeat('-', 60);
        $expectedString = "\n"
            . $varDump::class . '->process()'
            . "\n"
            . $hrLine
            . "\n"
            . __FILE__ . ':' . $line
            . "\n\n"
            . 'Arg•1 stdClass#' . spl_object_id($variable)
            . "\n" . $hrLine
            . "\n";
        $this->assertSame($expectedString, $writer->__toString());
    }

    public function testWithShift(): void
    {
        $stream = $this->getStream();
        $writer = new StreamWriter($stream);
        $varDump = $this->getVarDump();
        $varDumpWithShift = $varDump->withShift(1);
        $this->assertNotSame($varDump, $varDumpWithShift);
        $this->assertSame(1, $varDumpWithShift->shift());
        $varDumpWithShift->process($writer);
    }

    private function getVarDump(): VarDumpInterface
    {
        return new VarDump(new PlainFormat(), new PlainOutput());
    }

    private function getStream(): StreamInterface
    {
        return streamTemp('');
    }
}
