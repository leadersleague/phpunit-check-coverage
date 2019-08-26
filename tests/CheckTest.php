<?php

namespace Tests\Coverage;

use Coverage\Check;
use ErrorException;
use InvalidArgumentException;
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

    /**
     *
     */
    public function testCoverageFileNotExist(): void
    {
        try {
            (new Check())->run(__DIR__.'/fake', 50);
            throw new RuntimeException('test failed');
        } catch (InvalidArgumentException $exception) {
            $this->assertEquals($exception->getMessage(), 'Invalid path file: '.__DIR__.'/fake');
        }
    }
}
