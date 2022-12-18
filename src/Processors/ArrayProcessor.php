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
use Chevere\VarDump\Interfaces\ProcessorNestedInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use Chevere\VarDump\Processors\Traits\HandleDepthTrait;
use Chevere\VarDump\Processors\Traits\ProcessorTrait;

final class ArrayProcessor implements ProcessorInterface, ProcessorNestedInterface
{
    use ProcessorTrait;
    use HandleDepthTrait;

    /**
     * @var array<mixed>
     */
    private array $var;

    private int $count = 0;

    public function __construct(
        private VarDumperInterface $varDumper
    ) {
        $this->assertType();
        /** @var array<mixed> $array */
        $array = $this->varDumper->dumpable()->var();
        $this->var = $array;
        $this->depth = $this->varDumper->depth() + 1;
        $this->count = count($this->var);
        $this->info = 'size=' . $this->count;
    }

    public function type(): string
    {
        return TypeInterface::ARRAY;
    }

    public function write(): void
    {
        $this->varDumper->writer()->write(
            $this->typeHighlighted()
            . ' '
            . ($this->count === 0 ? '[] ' : '')
            . $this->highlightParentheses($this->info)
        );
        if ($this->isCircularRef($this->var)) {
            $this->varDumper->writer()->write(
                ' '
                . $this->highlightParentheses($this->circularReference())
            );

            return;
        }
        if ($this->depth > self::MAX_DEPTH) {
            $this->varDumper->writer()->write(
                ' '
                . $this->highlightParentheses($this->maxDepthReached())
            );

            return;
        }
        $this->processMembers();
    }

    /**
     * @param array<mixed> $array
     */
    private function isCircularRef(array $array): bool
    {
        foreach ($array as $var) {
            if ($array === $var) {
                return true;
            }
            if (is_array($var)) {
                return $this->isCircularRef($var);
            }
        }

        return false;
    }

    private function processMembers(): void
    {
        $operator = $this->highlightOperator('=>');
        foreach ($this->var as $key => $value) {
            $indentString = $this->varDumper->indentString();
            $format = $this->varDumper->format()
                ->getFilterEncodedChars((string) $key);
            $this->varDumper->writer()->write(
                "\n{$indentString}{$format} {$operator} "
            );

            $this->handleDepth($value);
        }
    }
}
