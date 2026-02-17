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

namespace Chevere\Tests\Processors;

use Chevere\Tests\Traits\VarDumperTrait;
use Chevere\VarDump\Processors\ArrayProcessor;
use PHPUnit\Framework\TestCase;
use TypeError;

final class ArrayProcessorTest extends TestCase
{
    use VarDumperTrait;

    public function testTypeError(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            <<<PLAIN
            Cannot assign null to property Chevere\VarDump\Processors\ArrayProcessor::\$var of type array
            PLAIN
        );
        new ArrayProcessor($this->getVarDumper(null));
    }

    public function testConstructEmpty(): void
    {
        $variable = [];
        $expectInfo = 'size=0';
        $varDumper = $this->getVarDumper($variable);
        $this->assertProcessor(ArrayProcessor::class, $varDumper);
        $processor = new ArrayProcessor($varDumper);
        $this->assertSame(1, $processor->depth());
        $this->assertSame($expectInfo, $processor->info());
        $this->assertSame(
            "array [] ({$expectInfo})",
            $varDumper->writer()->__toString()
        );
    }

    public function testX(): void
    {
        $variable = [0, 1, 2, 3];
        $expectInfo = 'size=' . count($variable);
        $containTpl = '%s => int %s (length=1)';
        $varDumper = $this->getVarDumper($variable);
        $processor = new ArrayProcessor($varDumper);
        $this->assertSame(1, $processor->depth());
        $this->assertSame($expectInfo, $processor->info());
        $processor->write();
        foreach ($variable as $int) {
            $this->assertStringContainsString(
                str_replace('%s', (string) $int, $containTpl),
                $varDumper->writer()->__toString()
            );
        }
    }

    public function testCircularReference(): void
    {
        $variable = [];
        $variable[] = &$variable;
        $expectInfo = 'size=' . count($variable);
        $varDumper = $this->getVarDumper($variable);
        $processor = new ArrayProcessor($varDumper);
        $this->assertSame($expectInfo, $processor->info());
        $this->assertSame(
            "array ({$expectInfo}) (" . $processor->circularReference() . ')',
            $varDumper->writer()->__toString()
        );
    }

    public function testDepth(): void
    {
        $variable = [];
        for ($i = 0; $i <= ArrayProcessor::MAX_DEPTH - 2; $i++) {
            $variable = [$variable];
        }
        $varDumper = $this->getVarDumper($variable);
        $processor = new ArrayProcessor($varDumper);
        $processor->write();
        $toString = $varDumper->writer()->__toString();
        $this->assertStringStartsWith(
            <<<PLAIN
            array (size=1)
             0 => array (size=1)
            PLAIN,
            $toString
        );
        $this->assertStringEndsWith(
            <<<PLAIN
            0 => array [] (size=0)
            PLAIN,
            $toString
        );
    }

    public function testMaxDepth(): void
    {
        $variable = [];
        for ($i = 0; $i <= ArrayProcessor::MAX_DEPTH; $i++) {
            $variable = [$variable];
        }
        $varDumper = $this->getVarDumper($variable);
        $processor = new ArrayProcessor($varDumper);
        $processor->write();
        $toString = $varDumper->writer()->__toString();
        $this->assertStringStartsWith(
            <<<PLAIN
            array (size=1)
             0 => array (size=1)
            PLAIN,
            $toString
        );
        $this->assertStringEndsWith(
            <<<PLAIN
            0 => array (size=1) ({$processor->maxDepthReached()})
            PLAIN,
            $toString
        );
    }
}
