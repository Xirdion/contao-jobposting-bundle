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
use Contao\Controller;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\Input;
use Contao\ModuleModel;
use Contao\StringUtil;
use Contao\Template;
use Dreibein\JobpostingBundle\Job\JobParser;
use Dreibein\JobpostingBundle\Job\JsonParser;
use Dreibein\JobpostingBundle\Model\JobModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @FrontendModule(category="jobs")
 */
class JobReaderController extends AbstractFrontendModuleController
{
    protected JobParser $jobParser;
    protected JsonParser $jsonParser;

    /**
     * JobReaderController constructor.
     *
     * @param JobParser  $jobParser
     * @param JsonParser $jsonParser
     */
    public function __construct(JobParser $jobParser, JsonParser $jsonParser)
    {
        $this->jobParser = $jobParser;
        $this->jsonParser = $jsonParser;
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
        global $objPage;
        $page = $this->getPageModel();
        if (null === $page) {
            throw new PageNotFoundException('Page not found: ' . $request->getUri());
        }

        $template->job = '';
        $template->referer = 'javascript:history.go(-1)';
        $template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];

        // Try to find the current job
        $param = Config::get('useAutoItem') ? 'auto_item' : 'items';
        $alias = (string) Input::get($param);
        $pids = StringUtil::deserialize($model->job_archives);
        $job = JobModel::findPublishedByAliasAndPids($alias, $pids);
        if (null === $job) {
            throw new PageNotFoundException('Page not found: ' . $request->getUri());
        }

        $this->jobParser->init($model, $page);
        $template->job = $this->jobParser->getJobData($job);
        $template->json = $this->jsonParser->parseJob($job);

        // TODO: with Contao 4.12 you can use the Contao\CoreBundle\Routing\ResponseContext\ResponseContextAccessor::class
        // Overwrite the page meta data
        // page title
        if ($job->getPageTitle()) {
            $objPage->pageTitle = $job->getPageTitle(); // Already stored decoded
        } elseif ($job->getTitle()) {
            $objPage->pageTitle = strip_tags(StringUtil::stripInsertTags($job->getTitle()));
        }

        // page description
        if ($job->getDescription()) {
            $objPage->description = $job->getDescription();
        } elseif ($job->getTeaser()) {
            $objPage->description = $this->prepareMetaDescription($job->getTeaser());
        }

        return $template->getResponse();
    }

    /**
     * Prepare a text to be used in the meta description tag.
     *
     * @param string $text
     *
     * @return string
     */
    protected function prepareMetaDescription(string $text): string
    {
        $text = Controller::replaceInsertTags($text, false);
        $text = strip_tags($text);
        $text = str_replace("\n", ' ', $text);
        $text = StringUtil::substr($text, 320);

        return trim($text);
    }
}
