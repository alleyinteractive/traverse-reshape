<?php

/*
 * This file is part of the traverse/reshape package.
 *
 * (c) Alley <info@alley.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Alley;

/**
 * Pluck one or many values from nested arrays or objects using 'dot' notation.
 *
 * @phpstan-return ($path is array ? mixed[] : mixed)
 *
 * @param mixed              $source    Data source.
 * @param int|string|mixed[] $path      The path or array of paths to values separated by $delimiter.
 *                                      Any path in array of paths can itself be an array of paths.
 * @param string             $delimiter Delimiter. Default is a '.'.
 * @return mixed The value or values if found or null.
 */
function traverse(mixed $source, array|int|string $path, string $delimiter = '.'): mixed
{
    return Internals\Traverser::createAndGet($source, $path, $delimiter);
}
