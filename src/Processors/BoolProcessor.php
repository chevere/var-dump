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

namespace Chevere\VarDump\Processors;

use Chevere\Parameter\Interfaces\TypeInterface;
use Chevere\VarDump\Interfaces\ProcessorInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use Chevere\VarDump\Processors\Traits\ProcessorTrait;

final class BoolProcessor implements ProcessorInterface
{
    use ProcessorTrait;

    public function __construct(
        private VarDumperInterface $varDumper
    ) {
        $this->assertType();
        $this->info = $this->varDumper->dumpable()->var() ? 'true' : 'false';
    }

    public function type(): string
    {
        return TypeInterface::BOOL;
    }

    public function write(): void
    {
        $this->varDumper->writer()->write(
            $this->typeHighlighted() . ' ' . $this->info
        );
    }
}
