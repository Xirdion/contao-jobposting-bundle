<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job posting bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\Job;

use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Slug\Slug;
use Dreibein\JobpostingBundle\Model\JobArchiveModel;
use Dreibein\JobpostingBundle\Model\JobModel;

class JobAliasGenerator
{
    private Slug $slug;

    public function __construct(Slug $slug)
    {
        $this->slug = $slug;
    }

    public function generateAlias(JobModel $job, string $title): string
    {
        // Declare the check-function for the slug generator
        $aliasExists = static function (string $alias) use ($job): bool {
            return JobModel::checkAlias($job->getId(), $alias) > 0;
        };

        // Generate alias
        $jobArchive = JobArchiveModel::findById($job->getPid());
        if (null === $jobArchive) {
            throw new AccessDeniedException(sprintf('Invalid job-archive ID "%s"', $job->getPid()));
        }

        return $this->slug->generate($title, $jobArchive->getJumpTo(), $aliasExists);
    }
}
