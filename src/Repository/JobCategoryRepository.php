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
