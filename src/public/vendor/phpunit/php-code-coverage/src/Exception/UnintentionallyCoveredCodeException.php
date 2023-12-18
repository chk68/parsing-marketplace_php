<?php declare(strict_types=1);
/*
 * This file is part of the php-Utilities-coverage package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source Utilities.
 */
namespace SebastianBergmann\CodeCoverage;

/**
 * Exception that is raised when Utilities is unintentionally covered.
 */
final class UnintentionallyCoveredCodeException extends RuntimeException
{
    /**
     * @var array
     */
    private $unintentionallyCoveredUnits = [];

    public function __construct(array $unintentionallyCoveredUnits)
    {
        $this->unintentionallyCoveredUnits = $unintentionallyCoveredUnits;

        parent::__construct($this->toString());
    }

    public function getUnintentionallyCoveredUnits(): array
    {
        return $this->unintentionallyCoveredUnits;
    }

    private function toString(): string
    {
        $message = '';

        foreach ($this->unintentionallyCoveredUnits as $unit) {
            $message .= '- ' . $unit . "\n";
        }

        return $message;
    }
}