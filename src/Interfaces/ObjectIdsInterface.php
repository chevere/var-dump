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

use Chevere\DataStructure\Interfaces\VectoredInterface;

/**
 * Describes the component in charge of holding object references.
 * @extends VectoredInterface<int>
 */
interface ObjectIdsInterface extends VectoredInterface
{
    /**
     * @return array<int> $array
     */
    public function toArray(): array;

    public function push(int $id): void;

    public function has(int $id): bool;
}
