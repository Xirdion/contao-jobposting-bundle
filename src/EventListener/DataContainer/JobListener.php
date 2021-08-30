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
use Contao\CoreBundle\Image\ImageSizes;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\LayoutModel;
use Contao\PageModel;
use DateTimeImmutable;
use Dreibein\JobpostingBundle\Job\AliasGenerator;
use Dreibein\JobpostingBundle\Job\UrlGenerator;
use Dreibein\JobpostingBundle\Model\JobCategoryModel;
use Dreibein\JobpostingBundle\Model\JobModel;
use Symfony\Contracts\Translation\TranslatorInterface;

class JobListener extends AbstractDcaListener
{
    private ImageSizes $imageSizes;
    private UrlGenerator $urlGenerator;
    private TranslatorInterface $translator;

    public function __construct(ImageSizes $imageSizes, UrlGenerator $urlGenerator, AliasGenerator $aliasGenerator, TranslatorInterface $translator)
    {
        parent::__construct($aliasGenerator);

        $this->imageSizes = $imageSizes;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    /**
     * @Callback(table="tl_job", target="fields.dateTime.load")
     *
     * @param $dateTime
     *
     * @return int
     */
    public function loadDateTimeField($dateTime): int
    {
        if (0 === (int) $dateTime) {
            return (new DateTimeImmutable())->getTimestamp();
        }

        return (int) $dateTime;
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
     * @Callback(table="tl_job", target="fields.categories.options")
     *
     * @return array
     */
    public function getCategoryOptions(): array
    {
        $categories = JobCategoryModel::findAll();
        if (null === $categories) {
            return [];
        }

        $data = [];
        foreach ($categories as $category) {
            $data[$category->getId()] = $category->getTitle();
        }

        return $data;
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
    public function updateToggleButton(array $record, ?string $href, string $label, string $title, ?string $icon, string $attributes): string
    {
        return $this->setToggleButton('tl_job', $record, $href, $label, $title, $icon, $attributes);
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
        $date = $date->setTimestamp((int) $record['dateTime']);

        return sprintf($html, $record['title'], $date->format(Config::get('datimFormat')));
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
        $page = PageModel::findById($archive->getJumpTo());
        if (null === $page) {
            return '';
        }

        // Load the layout of the page
        $page->loadDetails();
        $layout = LayoutModel::findById((int) $page->layoutId);
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
     * @Callback(table="tl_job", target="fields.type.options")
     *
     * @return array
     */
    public function getJobTypes(): array
    {
        $list = [];
        $lang = $GLOBALS['TL_LANGUAGE'] ?? 'en';
        $types = JobModel::TYPES;
        foreach ($types as $type) {
            $list[$type] = $this->translator->trans($type, [], 'DreibeinJobpostingBundle', $lang);
        }

        return $list;
    }
}
