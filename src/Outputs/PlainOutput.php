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

namespace Chevere\VarDump\Outputs;

class PlainOutput extends Output
{
    protected string $outputHr = '------------------------------------------------------------';

    public function finalize(): void
    {
        $this->writer()->write("\n" . $this->outputHr . "\n");
    }

    public function prepare(): void
    {
        $this->writer()->write(
            implode("\n", [
                '',
                $this->caller(),
                $this->outputHr,
            ])
        );
    }
}
