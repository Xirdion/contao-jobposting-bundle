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
use Contao\Model;
use Contao\Model\Collection;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Dreibein\JobpostingBundle\Model\JobCategoryModel;
use Dreibein\JobpostingBundle\Model\JobModel;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

class JobParser
{
    // private Studio $studio;
    private UrlGenerator $urlGenerator;
    private TranslatorInterface $translator;
    private string $projectDir;
    private Model $model;
    private ?PageModel $page;
    private bool $init = false;

    public function __construct(/* Studio $studio, */ UrlGenerator $urlGenerator, TranslatorInterface $translator, string $projectDir)
    {
        // $this->studio = $studio;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->projectDir = $projectDir;
    }

    public function init(Model $model, ?PageModel $page): void
    {
        $this->model = $model;
        $this->page = $page;
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
        $template = new FrontendTemplate($this->model->job_template ?: 'job_latest');

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

        $dateimFormat = $this->page->datimFormat ?? 'd.m.Y';
        $template->date = Date::parse($dateimFormat, $job->getDate());
        $template->timestamp = $job->getDate();
        $template->datetime = date('Y-m-d\TH:i:sP', $job->getDate());

        // location
        $template->street = $job->getStreet();
        $template->city = $job->getCity();
        $template->region = $job->getRegion();
        $template->postal = $job->getPostal();
        $template->country = System::getCountries()[$job->getCountry()];

        // Add an image
        $template->addImage = false;
        $template->addBefore = false;
        if ($job->isAddImage()) {
            $this->addImageToTemplate($job, $template);
        }

        // job data
        $lang = $GLOBALS['TL_LANGUAGE'] ?? 'en';

        // Prepare the job types with their translations
        $types = [];
        $jobTypes = $job->getJobType();
        foreach ($jobTypes as $jobType) {
            $types[$jobType] = $this->translator->trans('job.type.' . $jobType, [], 'DreibeinJobpostingBundle', $lang);
        }
        $template->job_type = $types;

        // Get the salary interval translation
        if ($job->getSalaryInterval()) {
            $template->salaryInterval = $this->translator->trans('job.salary_interval.' . $job->getSalaryInterval(), [], 'DreibeinJobpostingBundle', $lang);
        }

        // Format a given salary amount
        if ($job->getSalary()) {
            $fmt = new \NumberFormatter('de_DE', \NumberFormatter::CURRENCY);
            $template->salary = $fmt->formatCurrency($job->getSalary(), 'EUR');
        }

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

    /**
     * Create the picture for the template and add the image data to it.
     *
     * @param JobModel         $job
     * @param FrontendTemplate $template
     */
    private function addImageToTemplate(JobModel $job, FrontendTemplate $template): void
    {
        $image = FilesModel::findByUuid($job->getSingleSRC());

        if (null !== $image && is_file($this->projectDir . '/' . $image->path)) {
            $arrJob = $job->row();

            $imageSize = $job->getSize();
            if ($imageSize) {
                $size = StringUtil::deserialize($imageSize, true);

                if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]) || ($size[2][0] ?? null) === '_') {
                    $arrJob['size'] = $imageSize;
                }
            }

            $arrJob['singleSRC'] = $image->path;
            Controller::addImageToTemplate($template, $arrJob, null, null, $image);

            if (!$template->fullsize && !$template->imageUrl) {
                $picture = $template->picture;
                unset($picture['title']);
                $template->picture = $picture;

                $template->href = $template->link;
                $template->linkTitle = StringUtil::specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $job->getTitle()), true);
            }
        }
    }

    /*
    // this function is the approach for contao-version 4.11
    private function addImageToTemplate(JobModel $job, FrontendTemplate $template): void
    {
        // Get the image size from the job itself and override it with settings from the module if given
        $imgSize = $job->getSize() ?: null;
        if ($this->model->imgSize) {
            $size = StringUtil::deserialize($this->model->imgSize);

            if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]) || ($size[2][0] ?? null) === '_') {
                $imgSize = $this->model->imgSize;
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
