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
use function Chevere\VarDump\varDumpHtml;
use function Chevere\Writer\streamTemp;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Mimic xdebug var_dump example to showcase Chevere's var_dump.
 * https://xdebug.org/docs/develop#improved_var_dump
 */

class test
{
    public self $pub;

    protected $prot = 42;

    private $priv = true;

    public function __construct()
    {
        $this->pub = $this;
    }
}

$array = [
    'one' => 'a somewhat long string!',
    'two' => [
        'two.one' => [
            'two.one.zero' => 210,
            'two.one.one' => [
                'two.one.one.zero' => M_PI,
                'two.one.one.one' => 2.7,
            ],
        ],
    ],
    'three' => new test(),
    'four' => range(0, 5),
];

$filename = 'xdebug.html';
$varDump = varDumpHtml();
$writer = new StreamWriter(streamTemp(''));
$varDump
    ->withVariables($array)
    ->process($writer);
$dumping = str_replace(
    __DIR__,
    '/var/www/html',
    $writer->__toString()
);
file_put_contents(__DIR__ . '/output/' . $filename, $dumping);

var_dump($array);
vd($array);
exit();
