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
use Chevere\VarDump\Processors\IntProcessor;
use PHPUnit\Framework\TestCase;

final class IntProcessorTest extends TestCase
{
    use VarDumperTrait;

    public function testConstruct(): void
    {
        foreach ([0, 1, 100, 200, 110011] as $var) {
            $stringVar = (string) $var;
            $expectedInfo = 'length=' . strlen($stringVar);
            $varDumper = $this->getVarDumper($var);
            $this->assertProcessor(IntProcessor::class, $varDumper);
            $processor = new IntProcessor($varDumper);
            $this->assertSame($expectedInfo, $processor->info());
            $this->assertSame(
                "int {$stringVar} ({$expectedInfo})",
                $varDumper->writer()->__toString()
            );
        }
    }
}
