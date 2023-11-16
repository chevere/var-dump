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

use Chevere\VarDump\Interfaces\VarDumpInterface;
use LogicException;
use function Chevere\Message\message;

final class VarDumpInstance
{
    private static ?VarDumpInterface $instance;

    public function __construct(VarDumpInterface $varDump)
    {
        self::$instance = $varDump;
    }

    public static function get(): VarDumpInterface
    {
        if (! isset(self::$instance)) {
            throw new LogicException(
                (string) message('No instance')
            );
        }

        return self::$instance;
    }
}
