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
use Chevere\VarDump\Processors\NullProcessor;
use PHPUnit\Framework\TestCase;

final class NullProcessorTest extends TestCase
{
    use VarDumperTrait;

    public function testConstruct(): void
    {
        $variable = null;
        $varDumper = $this->getVarDumper($variable);
        $this->assertProcessor(NullProcessor::class, $varDumper);
        $processor = new NullProcessor($varDumper);
        $this->assertSame('', $processor->info());
        $this->assertSame('null', $varDumper->writer()->__toString());
    }
}
