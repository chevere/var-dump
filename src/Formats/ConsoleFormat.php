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
use Chevere\VarDump\Highlights\ConsoleHighlight;
use Chevere\VarDump\Interfaces\FormatInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;

final class ConsoleFormat implements FormatInterface
{
    use GetIndentTrait;
    use GetFilterEncodedCharsTrait;

    public function getEmphasis(string $string): string
    {
        return
            (new ConsoleHighlight(VarDumperInterface::EMPHASIS))
                ->getHighlight($string);
    }

    public function getHighlight(string $key, string $string): string
    {
        return
            (new ConsoleHighlight($key))
                ->getHighlight($string);
    }
}
