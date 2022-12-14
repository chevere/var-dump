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

use Colors\Color;
use Throwable;

final class ConsoleOutput extends Output
{
    private string $outputHr;

    public function tearDown(): void
    {
        $this->writer()->write("\n" . $this->outputHr . "\n");
    }

    public function prepare(): void
    {
        $color = new Color();
        $this->outputHr = str_repeat('-', 60);
        $caller = $this->caller();

        try {
            $this->outputHr = $color->fg('blue', $this->outputHr);
            // @phpstan-ignore-next-line
            $bold = $color->bold($caller);
            $caller = $color->fg('red', $bold);
        } catch (Throwable) { // @codeCoverageIgnoreStart
            // Ignore if color not supported
        }
        // @codeCoverageIgnoreEnd
        $this->writer()->write(
            implode("\n", [
                '',
                $caller,
                $this->outputHr,
            ])
        );
    }
}
