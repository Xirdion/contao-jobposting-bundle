<?php

declare(strict_types=1);

/*
 * This file is part of the job posting bundle.
 *
 * @author     Thomas Dirscherl <https://github.com/xirdion>
 * @link       https://github.com/xirdion/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\Job;

use Contao\ContentModel;
use Contao\Controller;
use Contao\Date;
use Contao\FilesModel;
use Contao\Model;
use Contao\Model\Collection;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Dreibein\JobpostingBundle\Model\JobCategoryModel;
use Dreibein\JobpostingBundle\Model\JobModel;
use Symfony\Contracts\Translation\TranslatorInterface;

class JobParser
{
    private UrlGenerator $urlGenerator;
    private TranslatorInterface $translator;
    private string $projectDir;
    private Model $model;
    private ?PageModel $page;
    private bool $init = false;

    public function __construct(UrlGenerator $urlGenerator, TranslatorInterface $translator, string $projectDir)
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
     * Get a list of parsed job data arrays.
     *
     * @param JobModel[]|Collection $jobs
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getJobListData(Collection $jobs): array
    {
        // Check if the parser was initialized
        if (false === $this->init) {
            throw new \Exception('JobParser was not initialized correctly!');
        }

        $limit = $jobs->count();
        if ($limit < 1) {
            return [];
        }

        $count = 0;
        $jobList = [];
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
            // parse the templates for all the jobs
            $cssClass = ((1 === ++$count) ? 'first' : '') . ($count === $limit ? ' last' : '') . ((($count % 2) === 0) ? ' odd' : ' even');
            $jobList[$job->getId()] = $this->getJobData($job, $cssClass, $count);
        }

        return $jobList;
    }

    /**
     * @param JobModel $job
     * @param string   $cssClass
     * @param int      $count
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getJobData(JobModel $job, string $cssClass = '', int $count = 0): array
    {
        // Check if the parser was initialized
        if (false === $this->init) {
            throw new \Exception('JobParser was not initialized correctly!');
        }

        // Add all job data for the template to the array
        $data = $job->row();

        $categories = $job->getCategories();
        $categoryData = [];
        foreach ($categories as $categoryId) {
            $category = JobCategoryModel::findById((int) $categoryId);
            if ($category) {
                $categoryData[$category->getId()] = $category;
            }
        }
        $data['categories'] = $categoryData;

        if ('' !== $job->getCssClass()) {
            $cssClass = ' ' . $job->getCssClass() . $cssClass;
        }

        // Add some default values to the template
        $data['class'] = $cssClass;
        $data['headline'] = $job->getTitle();
        $data['linkHeadline'] = $this->generateLink($job->getTitle(), $job);
        $data['more'] = $this->generateLink($GLOBALS['TL_LANG']['MSC']['more'], $job, true);
        $data['link'] = $this->urlGenerator->generateJobUrl($job);
        $data['archive'] = $job->getArchive();
        $data['count'] = $count;
        $data['text'] = '';
        $data['hasText'] = false;
        $data['hasTeaser'] = false;

        if ('' !== $job->getTeaser()) {
            $data['hasTeaser'] = true;
            $data['teaser'] = StringUtil::encodeEmail(StringUtil::toHtml5($job->getTeaser()));
        }

        $id = $job->getId();
        $data['text'] = static function () use ($id) {
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

        $data['hasText'] = static function () use ($id) {
            return ContentModel::countPublishedByPidAndTable($id, 'tl_job');
        };

        $dateimFormat = $this->page->datimFormat ?? 'd.m.Y';
        $data['date'] = Date::parse($dateimFormat, $job->getDate());
        $data['timestamp'] = $job->getDate();
        $data['datetime'] = date('Y-m-d\TH:i:sP', $job->getDate());

        // location
        $data['street'] = $job->getStreet();
        $data['city'] = $job->getCity();
        $data['region'] = $job->getRegion();
        $data['postal'] = $job->getPostal();
        $data['country'] = System::getCountries()[$job->getCountry()];

        // Add an image
        $data['addImage'] = false;
        $data['addBefore'] = false;
        if ($job->isAddImage()) {
            $imageData = $this->getImageData($job);
            $data = array_merge($data, $imageData);
        }

        // job data
        $lang = $GLOBALS['TL_LANGUAGE'] ?? 'en';

        // Prepare the job types with their translations
        $types = [];
        $jobTypes = $job->getJobType();
        foreach ($jobTypes as $jobType) {
            $types[$jobType] = $this->translator->trans('job.type.' . $jobType, [], 'DreibeinJobpostingBundle', $lang);
        }
        $data['job_type'] = $types;

        // Get the salary interval translation
        if ($job->getSalaryInterval()) {
            $data['salaryInterval'] = $this->translator->trans('job.salary_interval.' . $job->getSalaryInterval(), [], 'DreibeinJobpostingBundle', $lang);
        }

        // Format a given salary amount
        if ($job->getSalary()) {
            $fmt = new \NumberFormatter('de_DE', \NumberFormatter::CURRENCY);
            $data['salary'] = $fmt->formatCurrency($job->getSalary(), 'EUR');
        }

        return $data;
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
            $isReadMore ? $link : '<span itemprop="headline">' . $link . '</span>',
            '<span class="invisible"> ' . $job->getTitle() . '</span>'
        );
    }

    /**
     * Create the picture-data for the job.
     *
     * @param JobModel $job
     *
     * @return array
     */
    private function getImageData(JobModel $job): array
    {
        // TODO: With Contao 4.11 you can use the Contao-Image-Studio
        $template = new \stdClass();
        $image = FilesModel::findByUuid($job->getSingleSRC());

        if (null !== $image && is_file($this->projectDir . '/' . $image->path)) {
            $arrJob = $job->row();

            $imageSize = $job->getSize();
            if ($this->model->imgSize) {
                // Override the default image size
                $imageSize = $this->model->imgSize;
            }
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

            return get_object_vars($template);
        }

        return [];
    }
}
