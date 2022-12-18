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

namespace Chevere\VarDump\Outputs;

use Chevere\VarDump\Interfaces\FormatInterface;
use Chevere\VarDump\Interfaces\OutputInterface;
use Chevere\Writer\Interfaces\WriterInterface;

abstract class Output implements OutputInterface
{
    private WriterInterface $writer;

    /**
     * @var array<array<string, mixed>>
     */
    private array $trace;

    private string $caller;

    final public function setUp(WriterInterface $writer, array $trace): void
    {
        $this->writer = $writer;
        $this->trace = $trace;
        $this->caller = '';
        if ($this->trace[0]['class'] ?? null) {
            $this->caller .= $this->trace[0]['class']
                . $this->trace[0]['type'];
        }
        if ($this->trace[0]['function'] ?? null) {
            $this->caller .= $this->trace[0]['function'] . '()';
        }
    }

    public function writeCallerFile(FormatInterface $format): void
    {
        $item = $this->trace[0] ?? null;
        if ($item !== null && isset($item['file'])) {
            $this->writer->write(
                "\n"
                . $format
                    ->getHighlight(
                        '_file',
                        $item['file'] . ':' . $item['line']
                    )
                . "\n"
            );
        }
    }

    final public function trace(): array
    {
        return $this->trace;
    }

    final public function caller(): string
    {
        return $this->caller;
    }

    final protected function writer(): WriterInterface
    {
        return $this->writer;
    }
}
