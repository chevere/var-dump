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

use Chevere\Writer\StreamWriter;
use function Chevere\VarDump\varDumpConsole;
use function Chevere\VarDump\varDumpHtml;
use function Chevere\VarDump\varDumpPlain;
use function Chevere\Writer\streamTemp;

require_once __DIR__ . '/../vendor/autoload.php';

foreach ([
    'console.log' => varDumpConsole(),
    'plain.txt' => varDumpPlain(),
    'html.html' => varDumpHtml(),
] as $filename => $varDump) {
    $writer = new StreamWriter(streamTemp(''));
    $varDump
        ->withVariables($varDump)
        ->process($writer);
    $dumping = str_replace(
        __DIR__,
        '/var/www/html',
        $writer->__toString()
    );
    if ($filename === 'console.log') {
        echo $dumping;
    }
    file_put_contents(__DIR__ . '/output/' . $filename, $dumping);
}
