<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job posting bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\Template;
use Dreibein\JobpostingBundle\Job\JobParser;
use Dreibein\JobpostingBundle\Model\JobModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ContentElement(category="jobs")
 */
class JobDisplayController extends AbstractContentElementController
{
    private JobParser $jobParser;

    public function __construct(JobParser $jobParser)
    {
        $this->jobParser = $jobParser;
    }

    /**
     * @throws \Exception
     */
    protected function getResponse(Template $template, ContentModel $model, Request $request): ?Response
    {
        $jobId = (int) $model->job_id;
        if (0 === $jobId) {
            throw new \Exception(sprintf('"%s" is not a valid job id!', $jobId));
        }

        $job = JobModel::findById($jobId);
        if (!$job) {
            throw new \Exception(sprintf('Job with id "%s" not found!', $jobId));
        }
        $template->job = $job;

        $page = $this->getPageModel();
        $this->jobParser->init($model, $page);
        $template->parsedJob = $this->jobParser->parseJob($job);

        return $template->getResponse();
    }
}
