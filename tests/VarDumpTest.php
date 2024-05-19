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
use Chevere\Writer\StreamWriter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use stdClass;
use function Chevere\Writer\streamTemp;

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
        $var = new stdClass();
        $varDump = $this->getVarDump();
        $varDumpWithVariables = $varDump->withVariables($var);
        $this->assertNotSame($varDump, $varDumpWithVariables);
        $this->assertEqualsCanonicalizing(
            [$var],
            $varDumpWithVariables->variables()
        );
        $varDumpWithVariables->process($writer);
        $line = strval(__LINE__ - 1);
        $className = $varDump::class;
        $fileLine = __FILE__ . ':' . $line;
        $objectId = spl_object_id($var);
        $expectedString = <<<PLAIN

        {$className}->process()
        ------------------------------------------------------------
        {$fileLine}

        Arg#1 stdClass#{$objectId}
        ------------------------------------------------------------

        PLAIN;
        $this->assertSame($expectedString, $writer->__toString());
    }

    public function testCircularReferenceArguments(): void
    {
        $var = new stdClass();
        $var->circular = $var;
        $var->string = 'test';
        $varDump = $this->getVarDump();
        $varDumpWithVariables = $varDump->withVariables($var, [$var]);
        $stream = $this->getStream();
        $writer = new StreamWriter($stream);
        $varDumpWithVariables->process($writer);
        $line = strval(__LINE__ - 1);
        $className = $varDump::class;
        $fileLine = __FILE__ . ':' . $line;
        $objectId = spl_object_id($var);
        $expectedString = <<<PLAIN

        {$className}->process()
        ------------------------------------------------------------
        {$fileLine}

        Arg#1 stdClass#{$objectId}
        public circular stdClass#{$objectId} (circular reference #{$objectId})
        public string string test (length=4)

        Arg#2 array (size=1)
        0 => stdClass#{$objectId}
         public circular stdClass#{$objectId} (circular reference #{$objectId})
         public string string test (length=4)
        ------------------------------------------------------------

        PLAIN;
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
