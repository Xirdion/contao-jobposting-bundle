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
use Dreibein\JobpostingBundle\Model\JobCategoryModel;

abstract class JobCategoryRepository extends AbstractAliasRepository
{
    protected static $strTable = 'tl_job_category';

    /**
     * Find a specific category by its ID.
     *
     * @param int $id
     *
     * @return static|null
     */
    public static function findById(int $id): ?JobCategoryModel
    {
        return static::findByPk($id);
    }

    /**
     * @param array $ids
     * @param array $options
     *
     * @return JobCategoryModel[]|Model\Collection|null
     */
    public static function findByIds(array $ids, array $options = [])
    {
        if (empty($ids) || !\is_array($ids)) {
            return null;
        }

        $t = static::$strTable;

        if (!isset($options['order'])) {
            $options['order'] = "$t.title";
        }

        return static::findBy(["$t.id IN (" . implode(',', array_map('intval', $ids)) . ')'], null, $options);
    }

    /**
     * Find all categories ordered by their titles.
     *
     * @param array $arrOptions
     *
     * @return static[]|Model\Collection|null
     */
    public static function findAll(array $arrOptions = []): ?Model\Collection
    {
        $arrOptions['order'] = 'title ASC';

        return parent::findAll($arrOptions);
    }
}
