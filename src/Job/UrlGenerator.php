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

use Contao\Config;
use Contao\PageModel;
use Dreibein\JobpostingBundle\Model\JobModel;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class UrlGenerator
{
    private array $urlCache = [];
    private Request $request;

    /**
     * @throws Exception
     */
    public function __construct(RequestStack $requestStack)
    {
        $request = $requestStack->getCurrentRequest();
        if (null === $request) {
            throw new Exception('Missing request!');
        }
        $this->request = $request;
    }

    /**
     * Generate a URL for a specific job entry.
     *
     * @param JobModel $job
     * @param bool     $isAbsolute
     *
     * @return string
     */
    public function generateJobUrl(JobModel $job, bool $isAbsolute = false): string
    {
        // Generate a cache key (used when generating multiple pages at once)
        $cacheKey = 'id_' . $job->getId() . ($isAbsolute ? '_absolute' : '');

        if (isset($this->urlCache[$cacheKey])) {
            return $this->urlCache[$cacheKey];
        }

        $this->urlCache[$cacheKey] = null;

        $archive = $job->getArchive();
        if (null === $archive) {
            return $this->ampersand($this->request->getUri());
        }

        $page = PageModel::findById($archive->getJumpTo());
        if (!$page instanceof PageModel) {
            $this->urlCache[$cacheKey] = $this->ampersand($this->request->getUri());
        } else {
            $params = (Config::get('useAutoItem') ? '/' : '/items/') . ($job->getAlias() ?: $job->getId());
            $url = ($isAbsolute ? $page->getAbsoluteUrl($params) : $page->getFrontendUrl($params));
            $this->urlCache[$cacheKey] = $this->ampersand($url);
        }

        return $this->urlCache[$cacheKey];
    }

    private function ampersand(string $strString, bool $encode = true): string
    {
        return preg_replace('/&(amp;)?/i', ($encode ? '&amp;' : '&'), $strString);
    }
}
