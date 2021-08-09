<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job posting bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\Controller\FrontendModule;

use Contao\Config;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\Input;
use Contao\ModuleModel;
use Contao\Pagination;
use Contao\StringUtil;
use Contao\Template;
use Dreibein\JobpostingBundle\Job\JobParser;
use Dreibein\JobpostingBundle\Model\JobModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JobListController extends AbstractFrontendModuleController
{
    private JobParser $jobParser;

    public function __construct(JobParser $jobParser)
    {
        $this->jobParser = $jobParser;
    }

    /**
     * @param Template    $template
     * @param ModuleModel $model
     * @param Request     $request
     *
     * @throws \Exception
     *
     * @return Response|null
     */
    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        // init an empty list of jobs for the template
        $template->jobs = [];
        $template->empty = $GLOBALS['TL_LANG']['MSC']['emptyList'];

        // Check if there are any published jobs within the archives
        $jobArchives = StringUtil::deserialize($model->job_archives, true);
        $totalJobs = JobModel::countPublishedByPids($jobArchives);
        if ($totalJobs < 1) {
            return $template->getResponse();
        }

        // Try to load the current page model
        global $objPage;
        if (null === $objPage) {
            return $template->getResponse();
        }

        // Prepare the module data
        $offset = (int) $model->skipFirst;
        $totalJobs -= $offset;
        $numberOfItems = (int) $model->numberOfItems;
        $limit = 0;
        if ($numberOfItems) {
            $limit = $numberOfItems;
        }
        $perPage = (int) $model->perPage;

        // Check if the results must be split into pages
        if ($perPage > 0 && (!isset($limit) || $numberOfItems > $perPage)) {
            // Adjust the overall limit
            if (isset($limit)) {
                $totalJobs = min($limit, $totalJobs);
            }

            // Get the current page
            $id = 'page_n' . $model->id;
            $page = Input::get($id) ?? 1;

            // Do not index or cache the page if the page number is outside the range
            if ($page < 1 || $page > max(ceil($totalJobs / $perPage), 1)) {
                throw new PageNotFoundException('Page not found: ' . $request->getUri());
            }

            // Add the pagination menu
            $pagination = new Pagination($totalJobs, $perPage, Config::get('maxPaginationLinks'), $id);
            $template->pagination = $pagination->generate("\n  ");
        }

        // Find all jobs for the current page
        $jobs = JobModel::findPublishedByPids($jobArchives, $limit, $offset, $model->job_order);
        if (null !== $jobs) {
            $this->jobParser->init($objPage, $model);
            $template->jobs = $this->jobParser->parseJobs($jobs);
        }

        return $template->getResponse();
    }
}