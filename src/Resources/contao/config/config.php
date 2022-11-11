<?php

declare(strict_types=1);

/*
 * This file is part of the job posting bundle.
 *
 * @author     Thomas Dirscherl <https://github.com/xirdion>
 * @link       https://github.com/xirdion/contao-jobposting-bundle
 */

use Dreibein\JobpostingBundle\Model\JobArchiveModel;
use Dreibein\JobpostingBundle\Model\JobCategoryModel;
use Dreibein\JobpostingBundle\Model\JobModel;
use Dreibein\JobpostingBundle\Widget\JobCategoryPickerWidget;
use Dreibein\JobpostingBundle\Widget\JobPickerWidget;

$GLOBALS['BE_MOD']['content']['jobs'] = [
    'tables' => ['tl_job_archive', 'tl_job_category', 'tl_job', 'tl_content'],
];

$GLOBALS['BE_FFL']['jobPicker'] = JobPickerWidget::class;
$GLOBALS['BE_FFL']['jobCategoryPicker'] = JobCategoryPickerWidget::class;

$GLOBALS['TL_MODELS']['tl_job_archive'] = JobArchiveModel::class;
$GLOBALS['TL_MODELS']['tl_job_category'] = JobCategoryModel::class;
$GLOBALS['TL_MODELS']['tl_job'] = JobModel::class;
