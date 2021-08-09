<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job posting bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

use Dreibein\JobpostingBundle\Model\JobArchiveModel;
use Dreibein\JobpostingBundle\Model\JobCategoryModel;
use Dreibein\JobpostingBundle\Model\JobModel;

$GLOBALS['BE_MOD']['content']['jobs'] = [
    'tables' => ['tl_job_archive', 'tl_job_category', 'tl_job', 'tl_content'],
];

$GLOBALS['TL_MODELS']['tl_job_archive'] = JobArchiveModel::class;
$GLOBALS['TL_MODELS']['tl_job_category'] = JobCategoryModel::class;
$GLOBALS['TL_MODELS']['tl_job'] = JobModel::class;
