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
 * Describes the component in charge of processing a variable of a known type.
 */
interface ProcessorInterface
{
    /**
     * Provides the variable type (primitive).
     */
    public function type(): string;

    /**
     * Provides info about the variable like `size=1`, `length=6`, 'Object #id'.
     */
    public function info(): string;

    /**
     * Provides a highlighted type.
     */
    public function typeHighlighted(): string;

    /**
     * Highlights the given operator `$string`.
     */
    public function highlightOperator(string $string): string;

    /**
     * Highlights and wraps in parentheses the given `$string`.
     */
    public function highlightParentheses(string $string): string;

    /**
     * Write the dump to the stream.
     */
    public function write(): void;
}
