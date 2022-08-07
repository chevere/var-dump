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

namespace Chevere\VarDump\Interfaces;

use Chevere\Writer\Interfaces\WriterInterface;

/**
 * Describes the component in charge of providing a `\var_dump()` replacement.
 */
interface VarDumpInterface
{
    public function __construct(
        FormatInterface $format,
        OutputInterface $output
    );

    /**
     * Return an instance with the specified `$variables`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$variables`.
     */
    public function withVariables(...$variables): self;

    /**
     * Return an instance with the specified `$shift` traces shifted.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$shift` traces shifted.
     *
     * This method removes `$shift` traces.
     */
    public function withShift(int $shift): self;

    /**
     * Process the dump writing
     */
    public function process(WriterInterface $writer): void;

    /**
     * Provides access to the dump variables.
     */
    public function variables(): array;

    /**
     * Provides access to the shift value.
     */
    public function shift(): int;
}
