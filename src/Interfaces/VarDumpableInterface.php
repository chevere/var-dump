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

/**
 * Describes the component in charge of handling dumpable variables.
 */
interface VarDumpableInterface
{
    /**
     * Provides access to the variable.
     */
    public function var(): mixed;

    /**
     * Provides access to the variable type.
     */
    public function type(): string;

    /**
     * Provides access to the processor name used for handling.
     */
    public function processorName(): string;
}
