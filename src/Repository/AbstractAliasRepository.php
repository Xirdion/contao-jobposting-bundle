<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job posting bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
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
