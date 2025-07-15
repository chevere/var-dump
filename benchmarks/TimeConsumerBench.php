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

namespace Chevere\Benchmarks;

use Chevere\VarDump\VarDumpInstance;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\VarDumper;
use function Chevere\Parameter\int;
use function Chevere\VarDump\varDumpPlain;

require_once 'vendor/autoload.php';

VarDumper::setHandler(function ($var) {
    $cloner = new VarCloner();
    $dumper = new CliDumper(fopen('php://output', 'w'));
    $dumper->dump($cloner->cloneVar($var));
});

new VarDumpInstance(
    varDumpPlain()
);

class TimeConsumerBench
{
    private array $var;

    public function __construct()
    {
        $this->var = [
            0 => true,
            1 => false,
            2 => 1,
            3 => [10.2, null, false, true],
            'var' => 'value',
            'int' => int(min: 1010),
            'nested' => [[[[0, 1]]]],
        ];
    }

    /**
     * @Revs(2500)
     * @Iterations(5)
     * @Subject var_dump
     */
    public function benchNative()
    {
        ob_start();
        var_dump($this->var);
        ob_end_clean();
    }

    /**
     * @Revs(2500)
     * @Iterations(5)
     * @Subject vd
     */
    public function benchChevere()
    {
        ob_start();
        vd($this->var);
        ob_end_clean();
    }

    /**
     * @Revs(2500)
     * @Iterations(5)
     * @Subject dump
     */
    public function benchSymfony()
    {
        ob_start();
        dump($this->var);
        ob_get_clean();
    }
}
