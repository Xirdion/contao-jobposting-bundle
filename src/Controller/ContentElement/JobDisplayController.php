<?php

declare(strict_types=1);

/*
 * This file is part of the job posting bundle.
 *
 * @author     Thomas Dirscherl <https://github.com/xirdion>
 * @link       https://github.com/xirdion/contao-jobposting-bundle
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
            return null;
        }

        // Check if the id is a valid job id
        $job = JobModel::findById($jobId);
        if (!$job) {
            return null;
        }

        $template->hasJob = false;

        // Check if it is possible to show the job
        if ($job->published) {
            $page = $this->getPageModel();
            $this->jobParser->init($model, $page);
            $template->hasJob = true;

            $template->job = $this->jobParser->getJobData($job);
            $template->json = $this->jsonParser->parseJob($job);
        }

        return $template->getResponse();
    }
}
