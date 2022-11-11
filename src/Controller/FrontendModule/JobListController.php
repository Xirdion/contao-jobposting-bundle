<?php

declare(strict_types=1);

/*
 * This file is part of the job posting bundle.
 *
 * @author     Thomas Dirscherl <https://github.com/xirdion>
 * @link       https://github.com/xirdion/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\Controller\FrontendModule;

use Contao\Config;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\Input;
use Contao\ModuleModel;
use Contao\Pagination;
use Contao\StringUtil;
use Contao\Template;
use Dreibein\JobpostingBundle\Job\JobParser;
use Dreibein\JobpostingBundle\Model\JobModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @FrontendModule(category="jobs")
 */
class JobListController extends AbstractFrontendModuleController
{
    protected JobParser $jobParser;
    protected ContaoFramework $framework;

    /**
     * @param JobParser       $jobParser
     * @param ContaoFramework $framework
     */
    public function __construct(JobParser $jobParser, ContaoFramework $framework)
    {
        $this->jobParser = $jobParser;
        $this->framework = $framework;
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

        // Check the settings of the module
        $orderFeatured = ('featured_first' === $model->job_featured);
        $onlyFeatured = null;
        if ('featured' === $model->job_featured) {
            $onlyFeatured = true;
        } elseif ('unfeatured' === $model->job_featured) {
            $onlyFeatured = false;
        }

        // Check if there are any published jobs within the archives
        $jobArchives = StringUtil::deserialize($model->job_archives, true);
        $jobAdapter = $this->framework->getAdapter(JobModel::class);
        $totalJobs = $jobAdapter->countPublishedByPids($jobArchives, $onlyFeatured);
        if ($totalJobs < 1) {
            return $template->getResponse();
        }

        // Try to load the current page model
        $page = $this->getPageModel();
        if (null === $page) {
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
            $currentPage = Input::get($id) ?? 1;

            // Do not index or cache the page if the page number is outside the range
            if ($currentPage < 1 || $currentPage > max(ceil($totalJobs / $perPage), 1)) {
                throw new PageNotFoundException('Page not found: ' . $request->getUri());
            }

            // Add the pagination menu
            $pagination = new Pagination($totalJobs, $perPage, Config::get('maxPaginationLinks'), $id);
            $template->pagination = $pagination->generate("\n  ");
        }

        // Find all jobs for the current page
        $this->jobs = $jobAdapter->findPublishedByPids($jobArchives, $onlyFeatured, $limit, $offset, $model->job_order, $orderFeatured);
        if (null !== $this->jobs) {
            $this->jobParser->init($model, $page);
            $template->jobs = $this->jobParser->getJobListData($this->jobs);
        }

        return $template->getResponse();
    }
}
