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
use Chevere\VarDump\Formats\ConsoleFormat;
use Chevere\VarDump\Formats\HtmlFormat;
use Chevere\VarDump\Formats\PlainFormat;
use Chevere\VarDump\Outputs\ConsoleOutput;
use Chevere\VarDump\Outputs\HtmlOutput;
use Chevere\VarDump\Outputs\PlainOutput;
use Chevere\VarDump\VarDump;
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

$console = new VarDump(new ConsoleFormat(), new ConsoleOutput());
$plain = new VarDump(new PlainFormat(), new PlainOutput());
$html = new VarDump(new HtmlFormat(), new HtmlOutput());
foreach ([
    'console.log' => $console,
    'plain.txt' => $plain,
    'html.html' => $html,
] as $filename => $varDump) {
    $writer = new StreamWriter(streamTemp(''));
    $varDump->withVars($varDump)->process($writer);
    $dumping = stripLocal($writer->__toString());
    if ($filename == 'console.log') {
        echo $dumping;
        echo "\n";
        // dump($varDump);
    }
    $file = fileForPath(__DIR__ . '/output/' . $filename);
    $file->createIfNotExists();
    $file->put($dumping);
}
