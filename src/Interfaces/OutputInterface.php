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
 * Describes the component in charge of orchestrating the var dump output processing.
 */
interface OutputInterface
{
    /**
     * This method is executed before `prepare()`.
     * @param array<array<string, mixed>> $trace
     */
    public function setUp(WriterInterface $writer, array $trace): void;

    /**
     * Ends the output.
     */
    public function finalize(): void;

    /**
     * Writes the caller file using the target formatter.
     */
    public function writeCallerFile(FormatInterface $format): void;

    /**
     * Provides access to the instance backtrace.
     * @return array<array<string, mixed>>
     */
    public function trace(): array;

    /**
     * Provides access to the instance caller.
     */
    public function caller(): string;

    /**
     * This method is executed before `finalize()`.
     */
    public function prepare(): void;
}
