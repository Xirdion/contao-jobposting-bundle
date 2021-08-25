<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job posting bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\EventListener;

use Contao\Config;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Database;
use Contao\PageModel;
use Dreibein\JobpostingBundle\Model\JobArchiveModel;
use Dreibein\JobpostingBundle\Model\JobModel;

/**
 * @Hook("getSearchablePages")
 */
class GetSearchablePagesListener
{
    /**
     * @param array       $pages
     * @param int|null    $rootId
     * @param bool        $isSitemap
     * @param string|null $language
     *
     * @return array
     */
    public function __invoke(array $pages, int $rootId = null, bool $isSitemap = false, string $language = null): array
    {
        $rootPages = [];
        // add the roo
        if (null !== $rootId) {
            $database = Database::getInstance();
            $rootPages = $database->getChildRecords([$rootId], 'tl_page');
        }

        $processed = [];
        $time = time();

        $archives = JobArchiveModel::findAll();
        if (null === $archives) {
            return $pages;
        }

        foreach ($archives as $archive) {
            // Archive has no forwarding page
            if (0 === $archive->getJumpTo()) {
                continue;
            }

            // Jobs of this archive are outside the current root
            if (!empty($rootPages) && !\in_array($archive->getJumpTo(), $rootPages, true)) {
                continue;
            }

            // Check if the page was already processed
            if (!isset($processed[$archive->getJumpTo()])) {
                $this->addParentPageUrl($archive, $isSitemap, $time, $processed);
            }

            $url = $processed[$archive->getJumpTo()];
            $jobs = JobModel::findPublishedByPids([$archive->getId()]);
            if (null === $jobs) {
                continue;
            }

            // Add the link to the job posting to the list
            foreach ($jobs as $job) {
                $pages[] = sprintf(preg_replace('/%(?!s)/', '%%', $url), ($job->getAlias() ?: $job->getId()));
            }
        }

        return $pages;
    }

    /**
     * @param JobArchiveModel $archive
     * @param bool            $isSitemap
     * @param int             $time
     * @param array           $processed
     */
    private function addParentPageUrl(JobArchiveModel $archive, bool $isSitemap, int $time, array &$processed): void
    {
        $parentPage = PageModel::findWithDetails($archive->getJumpTo());
        if (null === $parentPage) {
            return;
        }

        // Check if the page was already published
        if (
            !$parentPage->published
            || ($parentPage->start && $parentPage->start > $time)
            || ($parentPage->stop && $parentPage->stop <= $time)
        ) {
            return;
        }

        if ($isSitemap) {
            // Page is protected
            if ($parentPage->protected) {
                return;
            }

            // Page should not be part of the sitemap
            if ('noindex,nofollow' === $parentPage->robots) {
                return;
            }
        }

        $processed[$archive->getJumpTo()] = $parentPage->getAbsoluteUrl(Config::get('useAutoItem') ? '/%s' : '/items/%s');
    }
}
