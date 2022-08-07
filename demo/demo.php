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

use function Chevere\Filesystem\fileForPath;
use function Chevere\VarDump\varDumpConsole;
use function Chevere\VarDump\varDumpHtml;
use function Chevere\VarDump\varDumpPlain;
use function Chevere\Writer\streamTemp;
use Chevere\Writer\StreamWriter;

require_once __DIR__ . '/../vendor/autoload.php';

function stripLocal(string $document): string
{
    return str_replace(
        dirname(__DIR__) . '/demo/',
        '/var/www/html/',
        $document
    );
}
$console = varDumpConsole();
$plain = varDumpPlain();
$html = varDumpHtml();
foreach ([
    'console.log' => $console,
    'plain.txt' => $plain,
    'html.html' => $html,
] as $filename => $varDump) {
    $writer = new StreamWriter(streamTemp(''));
    $varDump->withVariables($varDump)->process($writer);
    $dumping = stripLocal($writer->__toString());
    if ($filename == 'console.log') {
        echo $dumping;
    }
    $file = fileForPath(__DIR__ . '/output/' . $filename);
    $file->createIfNotExists();
    $file->put($dumping);
}
