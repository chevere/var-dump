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

namespace Chevere\VarDump\Formats\Traits;

/**
 * @codeCoverageIgnore
 */
trait DetailsTrait
{
    public function detailsOpen(bool $open = false): string
    {
        return '';
    }

    public function detailsClose(): string
    {
        return '';
    }

    public function detailsPullUp(): string
    {
        return '';
    }
}
