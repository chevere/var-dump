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
use Chevere\VarDump\Processors\BoolProcessor;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class BoolProcessorTest extends TestCase
{
    use VarDumperTrait;

    public function testConstruct(): void
    {
        foreach ([
            'true' => true,
            'false' => false,
        ] as $info => $var) {
            $varDumper = $this->getVarDumper($var);
            $this->assertProcessor(BoolProcessor::class, $varDumper);
            $processor = new BoolProcessor($varDumper);
            $this->assertSame($info, $processor->info(), 'info');
            $this->assertSame(
                "bool {$info}",
                $varDumper->writer()->__toString(),
            );
        }
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new BoolProcessor($this->getVarDumper(null));
    }
}
