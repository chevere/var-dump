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

final class StringProcessor implements ProcessorInterface
{
    use ProcessorTrait;

    public function __construct(
        private VarDumperInterface $varDumper
    ) {
        $this->assertType();
        /** @var string $string */
        $string = $this->varDumper->dumpable()->var();
        $this->info = 'length=' . mb_strlen($string);
    }

    public function type(): string
    {
        return TypeInterface::STRING;
    }

    public function write(): void
    {
        /** @var string $string */
        $string = $this->varDumper->dumpable()->var();
        $this->varDumper->writer()->write(
            implode(' ', [
                $this->typeHighlighted(),
                $this->varDumper->format()->getFilterEncodedChars($string),
                $this->highlightParentheses($this->info),
            ])
        );
    }
}
