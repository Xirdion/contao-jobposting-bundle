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

use Contao\ContentModel;
use Contao\Controller;
use Contao\Date;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\Model\Collection;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Dreibein\JobpostingBundle\Model\JobCategoryModel;
use Dreibein\JobpostingBundle\Model\JobModel;
use Exception;

class JobParser
{
    // private Studio $studio;
    private UrlGenerator $urlGenerator;
    private ModuleModel $module;
    private PageModel $page;
    private bool $init = false;

    public function __construct(/* Studio $studio, */ UrlGenerator $urlGenerator)
    {
        // $this->studio = $studio;
        $this->urlGenerator = $urlGenerator;
    }

    public function init(PageModel $page, ModuleModel $module): void
    {
        $this->page = $page;
        $this->module = $module;
        $this->init = true;
    }

    /**
     * Get a list of parsed templates for all jobs.
     *
     * @param JobModel[]|Collection $jobs
     *
     * @throws Exception
     *
     * @return array
     */
    public function parseJobs(Collection $jobs): array
    {
        // Check if the parser was initialized
        if (false === $this->init) {
            throw new Exception('JobParser was not initialized correctly!');
        }

        $limit = $jobs->count();
        if ($limit < 1) {
            return [];
        }

        $count = 0;
        $parsedArticles = [];
        $uuids = [];

        // Loop over all jobs to collect all uuids of the images
        foreach ($jobs as $job) {
            if (true === $job->isAddImage() && '' !== $job->getSingleSRC()) {
                $uuids[] = $job->getSingleSRC();
            }
        }

        // Preload all images in one query, so they are loaded into the model registry
        FilesModel::findMultipleByUuids($uuids);

        foreach ($jobs as $job) {
            // generate parse the templates for all the jobs
            $cssClass = ((1 === ++$count) ? 'first' : '') . (($count === $limit ? ' last' : '')) . ((($count % 2) === 0) ? ' odd' : ' even');
            $parsedArticles[] = $this->parseJob($job, $cssClass, $count);
        }

        return $parsedArticles;
    }

    /**
     * @param JobModel $job
     * @param string   $cssClass
     * @param int      $count
     *
     * @throws \Exception
     *
     * @return string
     */
    public function parseJob(JobModel $job, string $cssClass = '', int $count = 0): string
    {
        // Check if the parser was initialized
        if (false === $this->init) {
            throw new \Exception('JobParser was not initialized correctly!');
        }

        // Initialize the job detail template
        $template = new FrontendTemplate($this->module->job_template ?: 'job_latest');

        // Add all columns of the job to the template
        $template->setData($job->row());

        $categories = StringUtil::deserialize($template->categories, true);
        $categoryData = [];
        foreach ($categories as $categoryId) {
            $category = JobCategoryModel::findById((int) $categoryId);
            if ($category) {
                $categoryData[$category->getId()] = $category;
            }
        }
        $template->categories = $categoryData;

        if ('' !== $job->getCssClass()) {
            $cssClass = ' ' . $job->getCssClass() . $cssClass;
        }

        // Add some data to the template
        $template->class = $cssClass;
        $template->headline = $job->getTitle();
        $template->subHeadline = $job->getSubHeadline();
        $template->hasSubHeadline = ('' !== $job->getSubHeadline());
        $template->linkHeadline = $this->generateLink($job->getTitle(), $job);
        $template->more = $this->generateLink($GLOBALS['TL_LANG']['MSC']['more'], $job, true);
        $template->link = $this->urlGenerator->generateJobUrl($job);
        $template->archive = $job->getArchive();
        $template->count = $count;
        $template->text = '';
        $template->hasText = false;
        $template->hasTeaser = false;

        if ('' !== $job->getTeaser()) {
            $template->hasTeaser = true;
            $template->teaser = StringUtil::encodeEmail(StringUtil::toHtml5($job->getTeaser()));
        }

        $id = $job->getId();
        $template->text = static function () use ($id) {
            $text = '';
            $contentModels = ContentModel::findPublishedByPidAndTable($id, 'tl_job');
            if (null === $contentModels) {
                return $text;
            }

            // Collect all the content elements
            foreach ($contentModels as $contentModel) {
                $text .= Controller::getContentElement($contentModel);
            }

            return $text;
        };

        $template->hasText = static function () use ($id) {
            return ContentModel::countPublishedByPidAndTable($id, 'tl_job');
        };

        $template->date = Date::parse($this->page->datimFormat, $job->getDateTime());
        $template->timestamp = $job->getDateTime();
        $template->datetime = date('Y-m-d\TH:i:sP', $job->getDateTime());
        $template->addImage = false;
        $template->addBefore = false;

        // Add an image
        if ($job->isAddImage()) {
            // $this->addImageToTemplate($job, $template);
        }

        // TODO: Check for parseArticles Hook (like in ModuleNews)

        // TODO: Check caching (like in ModulesNews)

        return $template->parse();
    }

    /**
     * Universal function to generate a specific html link.
     *
     * @param string   $link
     * @param JobModel $job
     * @param bool     $isReadMore
     *
     * @return string
     */
    private function generateLink(string $link, JobModel $job, bool $isReadMore = false): string
    {
        return sprintf(
            '<a href="%s" title="%s" itemprop="url">%s%s</a>',
            $this->urlGenerator->generateJobUrl($job),
            StringUtil::specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $job->getTitle()), true),
            ($isReadMore ? $link : '<span itemprop="headline">' . $link . '</span>'),
            '<span class="invisible"> ' . $job->getTitle() . '</span>'
        );
    }

    /*
     * Create the picture for the template and add the image data to it.
     *
     * @param JobModel         $job
     * @param FrontendTemplate $template
     */
    /*
    private function addImageToTemplate(JobModel $job, FrontendTemplate $template): void
    {
        // Get the image size from the job itself and override it with settings from the module if given
        $imgSize = $job->getSize() ?: null;
        if ($this->module->imgSize) {
            $size = StringUtil::deserialize($this->module->imgSize);

            if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]) || ($size[2][0] ?? null) === '_') {
                $imgSize = $this->module->imgSize;
            }
        }

        // Generate the correct picture data
        $figureBuilder = $this->studio->createFigureBuilder();
        try {
            $figure = $figureBuilder
                ->fromUuid($job->getSingleSRC())
                ->setSize($imgSize)
                ->setMetadata($job->getOverwriteMetadata())
                ->enableLightbox($job->isFullsize())
                ->build()
            ;
        } catch (InvalidResourceException | \LogicException $e) {
            // builder was not able to generate the figure
            return;
        }

        // Rebuild with link to news article if none is set
        if (!$figure->getLinkHref()) {
            $linkTitle = StringUtil::specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $job->getTitle()), true);

            $figure = $figureBuilder
                ->setLinkHref($template->link)
                ->setLinkAttribute('title', $linkTitle)
                ->setOptions(['linkTitle' => $linkTitle]) // Backwards compatibility
                ->build()
            ;
        }

        // Use the legacy way to add the data to the template
        $figure->applyLegacyTemplateData($template, $job->getImagemargin(), $job->getFloating());
    }
    */
}
