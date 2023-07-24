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
use Chevere\VarDump\Interfaces\VarOutputInterface;
use Chevere\Writer\Interfaces\WriterInterface;

final class VarOutput implements VarOutputInterface
{
    /**
     * @param array<array<string, mixed>> $trace
     */
    public function __construct(
        private WriterInterface $writer,
        private array $trace,
        private FormatInterface $format,
    ) {
    }

    public function process(OutputInterface $output, mixed ...$variables): void
    {
        $output->setUp($this->writer, $this->trace);
        $output->prepare();
        $output->writeCallerFile($this->format);
        $this->handleArgs(...$variables);
        $output->tearDown();
    }

    private function handleArgs(mixed ...$variables): void
    {
        $aux = 0;
        foreach ($variables as $name => $value) {
            $aux++;
            if (is_int($name)) {
                $name = $aux;
            }
            $varDumper = new VarDumper(
                $this->writer,
                $this->format,
                new VarDumpable($value)
            );
            $this->writer->write(
                str_repeat("\n", (int) ($aux === 1 ?: 2))
                . 'Arg•' . strval($name) . ' '
            );
            $varDumper->withProcess();
        }
    }
}
