<?php

namespace Tests\Coverage;

use Coverage\Check;
use ErrorException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Class CheckTest
 */
class CheckTest extends TestCase
{
    /**
     * @throws ErrorException
     */
    public function testCoverageSuccess(): void
    {
        $coverage = (new Check())->run(__DIR__.'/coverage.xml', 48);
        $this->assertGreaterThan(48, $coverage);
    }

    /**
     *
     */
    public function testCoverageFail(): void
    {
        try {
            (new Check())->run(__DIR__.'/coverage.xml', 50);
            throw new RuntimeException('test failed');
        } catch (ErrorException $exception) {
            $this->assertEquals($exception->getMessage(), 'Code coverage is 49 percent, accepted is 50 percent');
        }
    }
}
