<?php

declare(strict_types=1);

/*
 * This file is part of the job posting bundle.
 *
 * @author     Thomas Dirscherl <https://github.com/xirdion>
 * @link       https://github.com/xirdion/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\Repository;

use Contao\Model;

abstract class AbstractAliasRepository extends Model implements AliasInterface
{
    /**
     * Count all entries by alias-field.
     *
     * @param int    $id
     * @param string $alias
     *
     * @return int
     */
    public static function checkAlias(int $id, string $alias): int
    {
        return static::countBy(['id != ?', 'alias = ?'], [$id, $alias]);
    }
}
