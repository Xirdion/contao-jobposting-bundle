<?php

declare(strict_types=1);

/*
 * This file is part of the job posting bundle.
 *
 * @author     Thomas Dirscherl <https://github.com/xirdion>
 * @link       https://github.com/xirdion/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\Job;

class Job
{
    public const TYPES = [
        'FULL_TIME',
        'PART_TIME',
        'CONTRACTOR',
        'TEMPORARY',
        'INTERN',
        'VOLUNTEER',
        'PER_DIEM',
        'OTHER',
    ];

    public const SALARY_INTERVAL = [
        'HOUR',
        'DAY',
        'WEEK',
        'MONTH',
        'YEAR',
    ];
}
