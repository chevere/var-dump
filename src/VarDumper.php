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

namespace Chevere\VarDump;

use Chevere\DataStructure\Interfaces\VectorInterface;
use Chevere\DataStructure\Vector;
use Chevere\Type\Interfaces\TypeInterface;
use Chevere\VarDump\Interfaces\FormatInterface;
use Chevere\VarDump\Interfaces\ProcessorInterface;
use Chevere\VarDump\Interfaces\VarDumpableInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use Chevere\Writer\Interfaces\WriterInterface;

final class VarDumper implements VarDumperInterface
{
    /**
     * @var VectorInterface<int>
     */
    public VectorInterface $knownObjectsId;

    private int $indent = 0;

    private string $indentString = '';

    private int $depth = -1;

    public function __construct(
        private WriterInterface $writer,
        private FormatInterface $format,
        private VarDumpableInterface $dumpable
    ) {
        $this->knownObjectsId = new Vector();
        ++$this->depth;
    }

    public function writer(): WriterInterface
    {
        return $this->writer;
    }

    public function format(): FormatInterface
    {
        return $this->format;
    }

    public function dumpable(): VarDumpableInterface
    {
        return $this->dumpable;
    }

    public function withIndent(int $indent): VarDumperInterface
    {
        $new = clone $this;
        $new->indent = $indent;
        $new->indentString = $new->format->getIndent($indent);

        return $new;
    }

    public function indent(): int
    {
        return $this->indent;
    }

    public function indentString(): string
    {
        return $this->indentString;
    }

    public function withDepth(int $depth): VarDumperInterface
    {
        $new = clone $this;
        $new->depth = $depth;

        return $new;
    }

    public function depth(): int
    {
        return $this->depth;
    }

    public function withKnownObjectsId(VectorInterface $ids): VarDumperInterface
    {
        $new = clone $this;
        $new->knownObjectsId = $ids;

        return $new;
    }

    public function knownObjectsId(): VectorInterface
    {
        return $this->knownObjectsId;
    }

    public function withProcess(): VarDumperInterface
    {
        $new = clone $this;
        $processorName = $new->dumpable->processorName();
        if (in_array($new->dumpable->type(), [TypeInterface::ARRAY, TypeInterface::OBJECT], true)) {
            ++$new->indent;
        }
        /** @var ProcessorInterface $processor */
        $processor = new $processorName($new);
        $processor->write();

        return $new;
    }
}
