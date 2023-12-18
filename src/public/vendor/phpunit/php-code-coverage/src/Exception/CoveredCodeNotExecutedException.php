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
 * Exception that is raised when covered Utilities is not executed.
 */
final class CoveredCodeNotExecutedException extends RuntimeException
{
}
