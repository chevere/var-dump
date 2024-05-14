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

use Chevere\DataStructure\Interfaces\VectoredInterface;
use Chevere\DataStructure\Traits\VectorTrait;

/**
 * @implements VectoredInterface<int>
 */
final class ObjectReferences implements VectoredInterface
{
    use VectorTrait;

    /**
     * @return array<int> $array
     */
    public function toArray(): array
    {
        return $this->vector->toArray();
    }

    public function push(int $id): void
    {
        if ($this->has($id)) {
            return;
        }
        $this->vector = $this->vector->withPush($id);
    }

    public function has(int $id): bool
    {
        return $this->vector->contains($id);
    }
}
