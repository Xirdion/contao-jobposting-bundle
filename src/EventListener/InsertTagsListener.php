<?php

declare(strict_types=1);

/*
 * This file is part of the job posting bundle.
 *
 * @author     Thomas Dirscherl <https://github.com/xirdion>
 * @link       https://github.com/xirdion/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\EventListener;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\StringUtil;
use Dreibein\JobpostingBundle\Job\UrlGenerator;
use Dreibein\JobpostingBundle\Model\JobModel;

/**
 * @Hook("replaceInsertTags")
 */
class InsertTagsListener
{
    /**
     * Define the supported insert tags.
     */
    private const SUPPORTED_TAGS = [
        'job',
        'job_open',
        'job_url',
        'job_title',
        'job_teaser',
    ];

    private ContaoFramework $framework;
    private UrlGenerator $urlGenerator;

    public function __construct(ContaoFramework $framework, UrlGenerator $urlGenerator)
    {
        $this->framework = $framework;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(string $tag, bool $useCache, $cacheValue, array $flags)
    {
        // Get the first part of the insert tag
        $elements = explode('::', $tag);
        $key = strtolower($elements[0]);

        // Return if the requested insert tag is not in the supported tags array
        if (!\in_array($key, self::SUPPORTED_TAGS, true)) {
            return false;
        }

        return $this->replaceJobsInsertTags($key, $elements[1], $flags);
    }

    private function replaceJobsInsertTags(string $insertTag, string $idOrAlias, array $flags): string
    {
        $this->framework->initialize();

        /** @var JobModel $adapter */
        $adapter = $this->framework->getAdapter(JobModel::class);

        if (null === ($job = $adapter->findByIdOrAlias($idOrAlias))) {
            return '';
        }

        $jobUrl = $this->urlGenerator->generateJobUrl($job, \in_array('absolute', $flags, true));
        $jobTitle = $job->getTitle();

        // Check requested insert tag and create response corresponding
        switch ($insertTag) {
            case 'job':
                return sprintf(
                    '<a href="%s" title="%s">%s</a>',
                    $jobUrl,
                    StringUtil::specialchars($jobTitle),
                    $jobTitle
                );
            case 'job_open':
                return sprintf(
                    '<a href="%s" title="%s">',
                    $jobUrl,
                    StringUtil::specialchars($jobTitle)
                );
            case 'job_url':
                return $jobUrl;
            case 'job_title':
                return StringUtil::specialchars($jobTitle);
            case 'job_teaser':
                return StringUtil::toHtml5($job->teaser);
        }

        return '';
    }
}
