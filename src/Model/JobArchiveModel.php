<?php

declare(strict_types=1);

/*
 * This file is part of the job posting bundle.
 *
 * @author     Thomas Dirscherl <https://github.com/xirdion>
 * @link       https://github.com/xirdion/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\Model;

use Dreibein\JobpostingBundle\Repository\JobArchiveRepository;

/**
 * Class JobArchiveModel.
 *
 * @property int     $tstamp
 * @property string  $title
 * @property string  $frontendTitle
 * @property int     $jumpTo
 * @property string  $apply_inactive_link
 * @property ?string $apply_inactive_text
 */
class JobArchiveModel extends JobArchiveRepository implements JobModelInterface
{
    /**
     * @return int
     */
    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * @return int
     */
    public function getTstamp(): int
    {
        return $this->tstamp;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getFrontendTitle(): string
    {
        return $this->frontendTitle ?: $this->title;
    }

    /**
     * @return int
     */
    public function getJumpTo(): int
    {
        return (int) $this->jumpTo;
    }

    /**
     * @return string
     */
    public function getApplyInactiveLink(): string
    {
        return $this->apply_inactive_link;
    }

    /**
     * @return string|null
     */
    public function getApplyInactiveText(): ?string
    {
        return $this->apply_inactive_text;
    }
}
