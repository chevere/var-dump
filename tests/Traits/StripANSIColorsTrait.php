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

namespace Chevere\Tests\Traits;

trait StripANSIColorsTrait
{
    private function stripANSIColors(string $string): string
    {
        return preg_replace('#\\x1b[[][^A-Za-z]*[A-Za-z]#', '', $string) ?? '';
    }
}