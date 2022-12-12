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

namespace Chevere\VarDump\Processors\Traits;

use Chevere\VarDump\Interfaces\VarDumperInterface;
use Chevere\VarDump\VarDumpable;
use Chevere\VarDump\VarDumper;

trait HandleDepthTrait
{
    private VarDumperInterface $varDumper;

    private function handleDepth(mixed $variable): void
    {
        $deep = $this->depth;
        if (is_scalar($variable)) {
            --$deep;
        }
        (new VarDumper(
            $this->varDumper->writer(),
            $this->varDumper->format(),
            new VarDumpable($variable),
        ))
            ->withDepth($deep)
            ->withIndent($this->varDumper->indent())
            ->withKnownObjects($this->varDumper->knownObjects())
            ->withProcess();
    }
}
