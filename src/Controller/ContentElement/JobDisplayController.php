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
use Dreibein\JobpostingBundle\Job\JsonParser;
use Dreibein\JobpostingBundle\Model\JobModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ContentElement(category="jobs")
 */
class JobDisplayController extends AbstractContentElementController
{
    private JobParser $jobParser;
    private JsonParser $jsonParser;

    /**
     * @param JobParser  $jobParser
     * @param JsonParser $jsonParser
     */
    public function __construct(JobParser $jobParser, JsonParser $jsonParser)
    {
        $this->jobParser = $jobParser;
        $this->jsonParser = $jsonParser;
    }

    /**
     * @throws \Exception
     */
    protected function getResponse(Template $template, ContentModel $model, Request $request): ?Response
    {
        // Get the id of the job to display
        $jobId = (int) $model->job_id;
        if (0 === $jobId) {
            throw new \Exception(sprintf('"%s" is not a valid job id!', $jobId));
        }

        // Check if the id is a valid job id
        $job = JobModel::findById($jobId);
        if (!$job) {
            throw new \Exception(sprintf('Job with id "%s" not found!', $jobId));
        }

        $template->hasJob = false;
        // only show the job, if it is published
        if ($job->published) {
            $page = $this->getPageModel();
            $this->jobParser->init($model, $page);
            $template->hasJob = true;

            $template->job = $this->jobParser->parseJob($job);
            $template->json = $this->jsonParser->parseJob($job);
        }

        return $template->getResponse();
    }
}
