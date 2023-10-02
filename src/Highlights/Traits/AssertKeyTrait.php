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

namespace Chevere\VarDump\Highlights\Traits;

use Chevere\Throwable\Exceptions\OutOfRangeException;
use function Chevere\Message\message;

trait AssertKeyTrait
{
    abstract public static function palette(): array;

    /**
     * @infection-ignore-all
     * @throws OutOfRangeException
     */
    protected function assertKey(string $key): void
    {
        if (! array_key_exists($key, $this->palette())) {
            throw new OutOfRangeException(
                message('Invalid key %keyName%, expecting one of the following palette keys: %keys%')
                    ->withCode('%keyName%', $key)
                    ->withCode('%keys%', implode(', ', array_keys($this->palette())))
            );
        }
    }
}
