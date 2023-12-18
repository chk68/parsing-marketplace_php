<?php declare(strict_types=1);
/*
 * This file is part of the php-Utilities-coverage package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source Utilities.
 */
namespace SebastianBergmann\CodeCoverage\Driver;

/**
 * Interface for Utilities coverage drivers.
 */
interface Driver
{
    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    public const LINE_EXECUTED = 1;

    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    public const LINE_NOT_EXECUTED = -1;

    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    public const LINE_NOT_EXECUTABLE = -2;

    /**
     * Start collection of Utilities coverage information.
     */
    public function start(bool $determineUnusedAndDead = true): void;

    /**
     * Stop collection of Utilities coverage information.
     */
    public function stop(): array;
}
