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

// @codeCoverageIgnoreStart

namespace Chevere\VarDump {
    use Chevere\Throwable\Exceptions\LogicException;
    use Chevere\VarDump\Formats\ConsoleFormat;
    use Chevere\VarDump\Formats\HtmlFormat;
    use Chevere\VarDump\Formats\PlainFormat;
    use Chevere\VarDump\Interfaces\VarDumpInterface;
    use Chevere\VarDump\Outputs\ConsoleOutput;
    use Chevere\VarDump\Outputs\HtmlOutput;
    use Chevere\VarDump\Outputs\PlainOutput;
    use Chevere\Writer\Interfaces\WritersInterface;
    use function Chevere\Writer\streamFor;
    use Chevere\Writer\StreamWriter;
    use Chevere\Writer\Writers;
    use Chevere\Writer\WritersInstance;

    function varDumpPlain(): VarDumpInterface
    {
        return
                new VarDump(
                    new PlainFormat(),
                    new PlainOutput()
                );
    }

    function varDumpConsole(): VarDumpInterface
    {
        return
            new VarDump(
                new ConsoleFormat(),
                new ConsoleOutput()
            );
    }

    function varDumpHtml(): VarDumpInterface
    {
        return
            new VarDump(
                new HtmlFormat(),
                new HtmlOutput()
            );
    }

    function varDump(): VarDumpInterface
    {
        try {
            return VarDumpInstance::get();
        } catch (LogicException $e) {
            return varDumpConsole();
        }
    }

    function getVarDumpWriters(): WritersInterface
    {
        try {
            return WritersInstance::get();
        } catch (LogicException $e) {
            return (new Writers())
                ->withOutput(
                    new StreamWriter(streamFor('php://stdout', 'r+'))
                )
                ->withError(
                    new StreamWriter(streamFor('php://stderr', 'r+'))
                );
        }
    }
}

namespace {
    use function Chevere\VarDump\getVarDumpWriters;
    use function Chevere\VarDump\varDump;

    if (!function_exists('vd')) {
        /**
         * Dumps information about one or more variables to the registered output writer stream
         */
        function vd(...$vars): void
        {
            varDump()
                ->withShift(1)
                ->withVars(...$vars)
                ->process(getVarDumpWriters()->output());
        }
    }
    if (!function_exists('vdd')) {
        /**
         * Dumps information about one or more variables to the registered output writer stream and die()
         * @codeCoverageIgnore
         */
        function vdd(...$vars): void
        {
            varDump()
                ->withShift(1)
                ->withVars(...$vars)
                ->process(getVarDumpWriters()->output());
            die(0);
        }
    }
}
// @codeCoverageIgnoreEnd
