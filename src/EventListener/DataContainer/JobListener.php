<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job posting bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\EventListener\DataContainer;

use Contao\BackendUser;
use Contao\Config;
use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Image\ImageSizes;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\Input;
use Contao\LayoutModel;
use Contao\PageModel;
use DateTimeImmutable;
use Dreibein\JobpostingBundle\Job\AliasGenerator;
use Dreibein\JobpostingBundle\Job\Job;
use Dreibein\JobpostingBundle\Job\UrlGenerator;
use Dreibein\JobpostingBundle\Model\JobCategoryModel;
use Dreibein\JobpostingBundle\Model\JobModel;
use Symfony\Contracts\Translation\TranslatorInterface;

class JobListener extends AbstractDcaListener
{
    private UrlGenerator $urlGenerator;
    private ImageSizes $imageSizes;
    private ContaoFramework $framework;
    private TranslatorInterface $translator;
    private string $lang;

    public function __construct(UrlGenerator $urlGenerator, AliasGenerator $aliasGenerator, ImageSizes $imageSizes, ContaoFramework $framework, TranslatorInterface $translator)
    {
        parent::__construct($aliasGenerator);

        $this->urlGenerator = $urlGenerator;
        $this->imageSizes = $imageSizes;
        $this->framework = $framework;
        $this->translator = $translator;
        $this->lang = $GLOBALS['TL_LANGUAGE'] ?? 'en';
    }

    /**
     * @Callback(table="tl_job", target="fields.apply_inactive_link.load")
     * @Callback(table="tl_job", target="fields.apply_inactive_text.load")
     *
     * @param string|null   $value
     * @param DataContainer $dc
     *
     * @return string
     */
    public function loadDefaultApplyInactiveValue(?string $value, DataContainer $dc): ?string
    {
        // Check if there is already a value in the database
        if ($value) {
            return $value;
        }

        if (null === $dc->activeRecord) {
            return $value;
        }

        // Load the job model
        $jobModel = $this->framework->getAdapter(JobModel::class);
        $job = $jobModel->findById((int) $dc->id);
        if (null === $job) {
            return $value;
        }

        // Load the job archive model
        $archive = $job->getArchive();
        if (null === $archive) {
            return $value;
        }

        // Select the correct function to load the field specific value
        switch ($dc->field) {
            case 'apply_inactive_link':
                return $archive->getApplyInactiveLink();
            case 'apply_inactive_text':
                return $archive->getApplyInactiveText();
        }

        return $value;
    }

    /**
     * @Callback(table="tl_job", target="fields.categories.options")
     *
     * @return array
     */
    public function getCategoryOptions(): array
    {
        $data = [];
        $input = $this->framework->getAdapter(Input::class);

        // Do not generate the options for other views than listings
        if ($input->get('act') && 'select' !== $input->get('act')) {
            return $data;
        }

        // Load all categories from the database
        $categoryModel = $this->framework->getAdapter(JobCategoryModel::class);
        $categories = $categoryModel->findAll();

        if (null === $categories) {
            return $data;
        }

        /** @var JobCategoryModel $category */
        foreach ($categories as $category) {
            $data[$category->getId()] = $category->getTitle();
        }

        return $data;
    }

    /**
     * @Callback(table="tl_job", target="fields.date.load")
     *
     * @param $dateTime
     *
     * @return int
     */
    public function loadJobDate($dateTime): int
    {
        $date = new DateTimeImmutable();
        if (0 !== (int) $dateTime) {
            $date = $date->setTimestamp((int) $dateTime);
        }

        // Set the time to 00:00:00
        $date = $date->setTime(0, 0, 0);

        return $date->getTimestamp();
    }

    /**
     * @Callback(table="tl_job", target="fields.time.load")
     *
     * @param $time
     *
     * @return int
     */
    public function loadJobTime($time): int
    {
        $date = new DateTimeImmutable();
        if (0 !== (int) $time) {
            $date = $date->setTimestamp((int) $time);
        }

        // Set date to 1970-01-01
        $date = $date->setDate(1970, 1, 1);

        return $date->getTimestamp();
    }

    /**
     * @Callback(table="tl_job", target="fields.start.load")
     * @Callback(table="tl_job", target="fields.stop.load")
     *
     * @param $dateTime
     *
     * @return string
     */
    public function loadStartStopFields($dateTime): string
    {
        if (0 === (int) $dateTime) {
            return '';
        }

        return (string) $dateTime;
    }

    /**
     * @Callback(table="tl_job", target="list.operations.toggle.button")
     *
     * @param array       $record
     * @param string|null $href
     * @param string      $label
     * @param string      $title
     * @param string|null $icon
     * @param string      $attributes
     *
     * @return string
     */
    public function updatePublishedButton(array $record, ?string $href, string $label, string $title, ?string $icon, string $attributes): string
    {
        $this->setToggleData('published', 'toggle', 'tl_job', 'visible.svg', 'invisible.svg');

        return $this->setToggleButton($record, $href, $label, $title, $icon, $attributes);
    }

    /**
     * @Callback(table="tl_job", target="list.operations.feature.button")
     *
     * @param array       $record
     * @param string|null $href
     * @param string      $label
     * @param string      $title
     * @param string|null $icon
     * @param string      $attributes
     *
     * @return string
     */
    public function updateFeaturedButton(array $record, ?string $href, string $label, string $title, ?string $icon, string $attributes): string
    {
        $this->setToggleData('featured', 'feature', 'tl_job', 'featured.svg', 'featured_.svg');

        return $this->setToggleButton($record, $href, $label, $title, $icon, $attributes);
    }

    /**
     * @Callback(table="tl_job", target="list.sorting.child_record")
     *
     * @param array $record
     *
     * @return string
     */
    public function listJobEntries(array $record): string
    {
        $html = '<div class="tl_content_left">%s <span style="color: #999;padding-left: 3px;">%s</span></div>';

        $date = new \DateTimeImmutable();
        $date = $date->setTimestamp((int) $record['date']);

        return sprintf($html, $record['title'], $date->format(Config::get('dateFormat')));
    }

    /**
     * @Callback(table="tl_job", target="fields.serpPreview.eval.url")
     *
     * @param JobModel $job
     *
     * @return string
     */
    public function getSerpUrl(JobModel $job): string
    {
        return $this->urlGenerator->generateJobUrl($job, true);
    }

    /**
     * @Callback(table="tl_job", target="fields.serpPreview.eval.title_tag")
     *
     * @param JobModel $job
     *
     * @return string
     */
    public function getSerpTitleTag(JobModel $job): string
    {
        // Try to load the archive of this job posting
        $archive = $job->getArchive();
        if (null === $archive) {
            return '';
        }

        // Try to load the forwarding page of the archive
        $pageModel = $this->framework->getAdapter(PageModel::class);
        $page = $pageModel->findById($archive->getJumpTo());
        if (null === $page) {
            return '';
        }

        // Load the layout of the page
        $page->loadDetails();
        $layoutModel = $this->framework->getAdapter(LayoutModel::class);
        $layout = $layoutModel->findById((int) $page->layoutId);
        if (null === $layout) {
            return '';
        }

        global $objPage;

        // Set the global page to use its data while replacing insert tags
        $objPage = $page;

        // Adjust the page title html
        $title = $layout->titleTag ?: '{{page::pageTitle}} - {{page::rootPageTitle}}';
        $title = str_replace('{{page::pageTitle}}', '%s', $title);

        return Controller::replaceInsertTags($title);
    }

    /**
     * @Callback(table="tl_job", target="fields.size.options")
     */
    public function getImageSizes(): array
    {
        return $this->imageSizes->getOptionsForUser(BackendUser::getInstance());
    }

    /**
     * Get the available types for a job.
     * These are defined by schema.org.
     *
     * @Callback(table="tl_job", target="fields.job_type.options")
     * @Callback(table="tl_content", target="fields.job_type.options")
     *
     * @return array
     */
    public function getJobTypes(): array
    {
        return $this->buildSelectionList(Job::TYPES, 'job.type.');
    }

    /**
     * Get the available salary intervals for a job.
     * These are defined by schema.org.
     *
     * @Callback(table="tl_job", target="fields.salaryInterval.options")
     *
     * @return array
     */
    public function getJobSalaryInterval(): array
    {
        return $this->buildSelectionList(Job::SALARY_INTERVAL, 'job.salary_interval.');
    }

    /**
     * @param array  $entries
     * @param string $translationId
     *
     * @return array
     */
    private function buildSelectionList(array $entries, string $translationId): array
    {
        $list = [];
        foreach ($entries as $entry) {
            $list[$entry] = $this->translator->trans($translationId . $entry, [], 'DreibeinJobpostingBundle', $this->lang);
        }

        return $list;
    }
}
