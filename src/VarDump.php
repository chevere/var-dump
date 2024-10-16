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

use Chevere\VarDump\Interfaces\FormatInterface;
use Chevere\VarDump\Interfaces\OutputInterface;
use Chevere\VarDump\Interfaces\VarDumpInterface;
use Chevere\Writer\Interfaces\WriterInterface;
use Chevere\Writer\StreamWriter;
use function Chevere\Writer\streamTemp;

final class VarDump implements VarDumpInterface
{
    /**
     * @var array<mixed>
     */
    private array $variables = [];

    private int $shift = 0;

    /**
     * @var array<array<string, mixed>>
     */
    private array $debugBacktrace = [];

    public function __construct(
        private FormatInterface $format,
        private OutputInterface $output
    ) {
    }

    public function withVariables(mixed ...$variables): VarDumpInterface
    {
        $new = clone $this;
        $new->variables = $variables;

        return $new;
    }

    public function withShift(int $shift): VarDumpInterface
    {
        $new = clone $this;
        $new->shift = $shift;

        return $new;
    }

    public function process(WriterInterface $writer): void
    {
        if ($this->variables === []) {
            return;
        }
        $this->setDebugBacktrace();
        (new VarOutput(
            $writer,
            $this->debugBacktrace,
            $this->format,
        ))
            ->process($this->output, ...$this->variables);
    }

    public function export(): string
    {
        $temp = streamTemp();
        $tempWriter = new StreamWriter($temp);
        $this->process($tempWriter);

        return $tempWriter->__toString();
    }

    public function variables(): array
    {
        return $this->variables;
    }

    public function shift(): int
    {
        return $this->shift;
    }

    /**
     * @infection-ignore-all
     */
    private function setDebugBacktrace(): void
    {
        $this->debugBacktrace = debug_backtrace();
        for ($i = 0; $i <= $this->shift; $i++) {
            array_shift($this->debugBacktrace);
        }
    }
}
