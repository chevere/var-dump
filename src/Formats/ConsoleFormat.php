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

use Chevere\VarDump\Formats\Traits\DetailsTrait;
use Chevere\VarDump\Formats\Traits\FilterEncodedCharsTrait;
use Chevere\VarDump\Formats\Traits\IndentTrait;
use Chevere\VarDump\Highlights\ConsoleHighlight;
use Chevere\VarDump\Interfaces\FormatInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;

final class ConsoleFormat implements FormatInterface
{
    use DetailsTrait;
    use IndentTrait;
    use FilterEncodedCharsTrait;

    public function emphasis(string $string): string
    {
        return
            (new ConsoleHighlight(VarDumperInterface::EMPHASIS))
                ->highlight($string);
    }

    public function highlight(string $key, string $string): string
    {
        return
            (new ConsoleHighlight($key))
                ->highlight($string);
    }
}
