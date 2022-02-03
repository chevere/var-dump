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

namespace Chevere\VarDump\Formats;

use Chevere\VarDump\Formats\Traits\GetFilterEncodedCharsTrait;
use Chevere\VarDump\Formats\Traits\GetIndentTrait;
use Chevere\VarDump\Interfaces\VarDumpFormatInterface;

final class VarDumpPlainFormat implements VarDumpFormatInterface
{
    use GetIndentTrait;

    use GetFilterEncodedCharsTrait;

    public function getHighlight(string $key, string $string): string
    {
        return $string;
    }

    public function getEmphasis(string $string): string
    {
        return $string;
    }
}
