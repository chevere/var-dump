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

namespace Chevere\Tests\Traits;

use Chevere\VarDump\Formats\PlainFormat;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use Chevere\VarDump\VarDumpable;
use Chevere\VarDump\VarDumper;
use function Chevere\Writer\streamTemp;
use Chevere\Writer\StreamWriter;

trait VarDumperTrait
{
    private function getVarDumper($var): VarDumperInterface
    {
        return (new VarDumper(
            new StreamWriter(streamTemp()),
            new PlainFormat(),
            new VarDumpable($var)
        ))->withProcess();
    }

    private function assertProcessor(
        string $processor,
        VarDumperInterface $varDumper
    ): void {
        $this->assertSame(
            $processor,
            $varDumper->dumpable()->processorName()
        );
    }
}
