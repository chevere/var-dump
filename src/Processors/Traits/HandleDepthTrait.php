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

use Chevere\VarDump\VarDumpable;
use Chevere\VarDump\VarDumper;

trait HandleDepthTrait
{
    private function handleDepth($var): void
    {
        $deep = $this->depth;
        if (is_scalar($var)) {
            --$deep;
        }
        (new VarDumper(
            $this->varDumper->writer(),
            $this->varDumper->format(),
            new VarDumpable($var),
        ))
            ->withDepth($deep)
            ->withIndent($this->varDumper->indent())
            ->withKnownObjects($this->varDumper->known())
            ->withProcess();
    }
}
