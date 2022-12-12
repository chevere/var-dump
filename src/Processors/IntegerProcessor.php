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

use Chevere\Type\Interfaces\TypeInterface;
use Chevere\VarDump\Interfaces\ProcessorInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use Chevere\VarDump\Processors\Traits\ProcessorTrait;

final class IntegerProcessor implements ProcessorInterface
{
    use ProcessorTrait;

    private string $stringVar = '';

    public function __construct(
        private VarDumperInterface $varDumper
    ) {
        $this->assertType();
        /** @var int $integer */
        $integer = $this->varDumper->dumpable()->var();
        $this->stringVar = strval($integer);
        $this->info = 'length=' . strlen($this->stringVar);
    }

    public function type(): string
    {
        return TypeInterface::INTEGER;
    }

    public function write(): void
    {
        $this->varDumper->writer()->write(
            implode(' ', [
                $this->typeHighlighted(),
                $this->varDumper->format()->getFilterEncodedChars($this->stringVar),
                $this->highlightParentheses($this->info),
            ])
        );
    }
}
