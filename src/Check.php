<?php

namespace Coverage;

use ErrorException;
use InvalidArgumentException;
use SimpleXMLElement;
use function sprintf;

/**
 * Class Check
 */
class Check
{
    /**
     * @param string $coverageFilePath
     * @param int $minPercent
     * @return string
     * @throws ErrorException
     */
    public function run(string $coverageFilePath, int $minPercent): string
    {
        if (!file_exists($coverageFilePath)) {
            throw new InvalidArgumentException('Invalid path file: '.$coverageFilePath);
        }

        $metrics = (new SimpleXMLElement(file_get_contents($coverageFilePath)))->xpath('//metrics');
        [$totalElements, $checkedElements] = $this->getTotals($metrics);
        $coverage = $this->getPercent($checkedElements, $totalElements);

        if ($coverage < $minPercent) {
            throw new ErrorException(
                sprintf('Code coverage is %d percent, accepted is %d percent', $coverage, $minPercent)
            );
        }

        return $coverage;
    }

    /**
     * @param int $checkedElements
     * @param int $totalElements
     * @return int
     */
    private function getPercent(int $checkedElements, int $totalElements): int
    {
        return ($checkedElements / $totalElements) * 100;
    }

    /**
     * @param array $metrics
     * @return array
     */
    private function getTotals(array $metrics): array
    {
        $totalElements = 0;
        $checkedElements = 0;

        foreach ($metrics as $metric) {
            $totalElements += (int)$metric['elements'];
            $checkedElements += (int)$metric['coveredelements'];
        }
        return array($totalElements, $checkedElements);
    }
}
