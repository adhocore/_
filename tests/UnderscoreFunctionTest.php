<?php

/*
 * This file is part of the PHP-UNDERSCORE package.
 *
 * (c) Jitendra Adhikari <jiten.adhikary@gmail.com>
 *     <https://github.com/adhocore>
 *
 * Licensed under MIT license.
 */

namespace Ahc\Underscore\Tests;

use PHPUnit\Framework\TestCase;

class UnderscoreFunctionTest extends TestCase
{
    public function test_memoize()
    {
        $memoSum = underscore()->memoize(function ($a, $b) {
            echo "sum $a + $b";

            return $a + $b;
        });

        // Every time the sum callback is called it echoes something.
        // But since it is memorized, it should only echo in the first call.
        ob_start();

        // Call 3 times!
        $this->assertSame(1 + 2, $memoSum(1, 2));
        $this->assertSame(1 + 2, $memoSum(1, 2));
        $this->assertSame(1 + 2, $memoSum(1, 2));

        // Call twice for different args!
        $this->assertSame(3 + 2, $memoSum(3, 2));
        $this->assertSame(3 + 2, $memoSum(3, 2));

        $buffer = ob_get_clean();

        $this->assertSame(
            1,
            substr_count($buffer, 'sum 1 + 2'),
            'Should be called only once, subsequent calls uses memo'
        );
        $this->assertSame(
            1,
            substr_count($buffer, 'sum 3 + 2'),
            'Should be called only once, subsequent calls uses memo'
        );
    }

    public function test_delay()
    {
        $callback = function () {
            // Do nothing!
        };

        // Calibrate time taken by callback!
        $cTime = microtime(1);
        $callback();
        $cTime = microtime(1) - $cTime;

        // Now delay this callback by 10millis (0.01sec).
        $delayCall = underscore()->delay($callback, 10);

        $time = microtime(1);
        $delayCall();
        $time = microtime(1) - $time;

        // The overall time must be >= (cTime + 1sec).
        $this->assertGreaterThanOrEqual(0.01 + $cTime, $time);
    }

    public function test_throttle()
    {
        $callback = function () {
            echo 'throttle';
        };

        // Throttle the call for once per 10millis (0.01 sec)
        // So that for a period of 300millis it should be actually called at most 3 times.
        $throtCall = underscore()->throttle($callback, 10);

        ob_start();

        $start = microtime(1);
        while (microtime(1) - $start <= 0.031) {
            $throtCall();
        }

        $buffer = ob_get_clean();

        $this->assertLessThanOrEqual(
            3,
            substr_count($buffer, 'throttle'),
            'Should be called only once, subsequent calls uses memo'
        );
    }

    public function test_compose()
    {
        $c = underscore()->compose('strlen', 'strtolower', 'strtoupper');

        $this->assertSame(7, $c('aBc.xYz'));
    }
}
