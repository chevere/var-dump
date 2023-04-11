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
 * Describes the component in charge of processing a variable with nesting (array, object)
 */
interface ProcessorNestedInterface
{
    public const MAX_DEPTH = 100;

    /**
     * Provides the current processor depth.
     */
    public function depth(): int;

    /**
     * Provides the `circular reference` flag.
     */
    public function circularReference(): string;

    /**
     * Provides the `max depth reached` flag.
     */
    public function maxDepthReached(): string;
}
