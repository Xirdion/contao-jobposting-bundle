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
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\CoreBundle\Slug\Slug;
use Contao\DataContainer;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\System;
use DateTimeImmutable;
use Dreibein\JobpostingBundle\Job\UrlGenerator;
use Dreibein\JobpostingBundle\Model\JobArchiveModel;
use Dreibein\JobpostingBundle\Model\JobCategoryModel;
use Dreibein\JobpostingBundle\Model\JobModel;
use Exception;

class JobListener extends AbstractDcaListener
{
    private Slug $slug;
    private UrlGenerator $urlGenerator;

    public function __construct(Slug $slug, UrlGenerator $urlGenerator)
    {
        $this->slug = $slug;
        $this->urlGenerator = $urlGenerator;
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
     * Auto-generate the news alias if it has not been set yet.
     *
     * @Callback(table="tl_job", target="fields.alias.save")
     *
     * @param mixed         $newAlias
     * @param DataContainer $dc
     *
     * @throws Exception
     *
     * @return string
     */
    public function generateAlias($newAlias, DataContainer $dc): string
    {
        // Declare the check-function for the slug generator
        $aliasExists = static function (string $alias) use ($dc): bool {
            return JobModel::checkAlias((int) $dc->id, $alias) > 0;
        };

        // Generate alias if there is none
        if (!$newAlias) {
            $id = (int) $dc->id;

            $job = JobModel::findById($id);
            if (null === $job) {
                throw new AccessDeniedException('Invalid job ID "' . $id . '".');
            }

            $jobArchive = JobArchiveModel::findById($job->getPid());
            if (null === $jobArchive) {
                throw new AccessDeniedException('Invalid job-archive ID "' . $job->getPid() . '".');
            }

            $newAlias = $this->slug->generate($dc->activeRecord->title, $jobArchive->getJumpTo(), $aliasExists);
        } elseif (preg_match('/^[1-9]\d*$/', $newAlias)) {
            throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasNumeric'], $newAlias));
        } elseif ($aliasExists($newAlias)) {
            throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $newAlias));
        }

        return $newAlias;
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
        return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
    }

    /**
     * @Callback(table="tl_job", target="fields.addImage.save")
     *
     * @param string $checked
     *
     * @return int
     */
    public function saveAddImageField(string $checked): int
    {
        return (int) (bool) $checked;
    }
}
