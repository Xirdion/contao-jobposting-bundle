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
use Dreibein\JobpostingBundle\Model\JobArchiveModel;

abstract class JobArchiveRepository extends Model
{
    protected static $strTable = 'tl_job_archive';

    /**
     * Find a specific archive by its ID.
     *
     * @param int $id
     *
     * @return static|null
     */
    public static function findById(int $id): ?JobArchiveModel
    {
        return static::findByPk($id);
    }

    /**
     * @param array $arrOptions
     *
     * @return static[]|Model\Collection|null
     */
    public static function findAll(array $arrOptions = []): ?Model\Collection
    {
        return parent::findAll($arrOptions);
    }

    public static function findByIds(array $ids, array $arrOptions = [])
    {
        if (empty($ids) || !\is_array($ids)) {
            return null;
        }

        $t = static::$strTable;

        if (!isset($arrOptions['order'])) {
            $arrOptions['order'] = "$t.title";
        }

        return static::findBy(["$t.id IN (" . implode(',', array_map('intval', $ids)) . ')'], null, $arrOptions);
    }
}
