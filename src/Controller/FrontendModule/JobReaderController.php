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
use Contao\Controller;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\Input;
use Contao\ModuleModel;
use Contao\StringUtil;
use Contao\Template;
use Dreibein\JobpostingBundle\Job\JobParser;
use Dreibein\JobpostingBundle\Model\JobModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @FrontendModule(category="jobs")
 */
class JobReaderController extends AbstractFrontendModuleController
{
    private JobParser $jobParser;

    /**
     * JobReaderController constructor.
     *
     * @param JobParser $jobParser
     */
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
        global $objPage;
        if (null === $objPage) {
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

        // Set the default template
        if ('' === $model->job_template) {
            $model->job_template = 'job_full';
        }

        $this->jobParser->init($model, $objPage);
        $template->job = $this->jobParser->parseJob($job);

        // Overwrite the page title
        if ($job->getTitle()) {
            $objPage->pageTitle = $job->getTitle();
        }

        // Overwrite the page description
        if ($job->getTeaser()) {
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
