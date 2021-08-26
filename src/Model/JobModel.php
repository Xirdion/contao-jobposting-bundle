<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job posting bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\Model;

use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\StringUtil;
use Dreibein\JobpostingBundle\Repository\JobRepository;

/**
 * Class JobModel.
 *
 * @property int    $pid
 * @property int    $tstamp
 * @property string $title
 * @property string $alias
 * @property string $categories
 * @property int    $dateTime
 * @property string $subHeadline
 * @property string $teaser
 * @property string $company
 * @property string $companyLogo
 * @property string $type
 * @property string $times
 * @property string $postal
 * @property string $city
 * @property string $street
 * @property string $region
 * @property string $country
 * @property bool   $remote
 * @property string $salary
 * @property string $salaryInterval
 * @property string $responsibility
 * @property string $skills
 * @property string $qualification
 * @property string $education
 * @property string $experience
 * @property bool   $addImage
 * @property string $singleSRC
 * @property string $size
 * @property string $floating
 * @property string $imagemargin
 * @property bool   $fullsize
 * @property bool   $overwriteMeta
 * @property string $alt
 * @property string $imageTitle
 * @property string $imageUrl
 * @property string $caption
 * @property string $cssClass
 * @property bool   $featured
 * @property bool   $published
 * @property int    $start
 * @property int    $stop
 */
class JobModel extends JobRepository
{
    // TODO: missing ModelMetadataTrait - not available for contao 4.9

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
    public function getPid(): int
    {
        return (int) $this->pid;
    }

    /**
     * @return JobArchiveModel|null
     */
    public function getArchive(): ?JobArchiveModel
    {
        return JobArchiveModel::findById($this->getPid());
    }

    /**
     * @return int
     */
    public function getTstamp(): int
    {
        return (int) $this->tstamp;
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
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        return StringUtil::deserialize($this->categories, true);
    }

    /**
     * @return int
     */
    public function getDateTime(): int
    {
        return (int) $this->dateTime;
    }

    /**
     * @return string
     */
    public function getSubHeadline(): string
    {
        return $this->subHeadline;
    }

    /**
     * @return string
     */
    public function getTeaser(): string
    {
        return (string) $this->teaser;
    }

    /**
     * @return bool
     */
    public function isAddImage(): bool
    {
        return (bool) $this->addImage;
    }

    /**
     * @return string|null
     */
    public function getSingleSRC(): ?string
    {
        return $this->singleSRC;
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getFloating(): string
    {
        return $this->floating;
    }

    /**
     * @return string
     */
    public function getImagemargin(): string
    {
        return $this->imagemargin;
    }

    /**
     * @return bool
     */
    public function isFullsize(): bool
    {
        return (bool) $this->fullsize;
    }

    /**
     * @return bool
     */
    public function isOverwriteMeta(): bool
    {
        return (bool) $this->overwriteMeta;
    }

    /**
     * @return string
     */
    public function getAlt(): string
    {
        return $this->alt;
    }

    /**
     * @return string
     */
    public function getImageTitle(): string
    {
        return $this->imageTitle;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @return string
     */
    public function getCaption(): string
    {
        return $this->caption;
    }

    /**
     * @return string
     */
    public function getCssClass(): string
    {
        return $this->cssClass;
    }

    /**
     * @return bool
     */
    public function isFeatured(): bool
    {
        return (bool) $this->featured;
    }

    /**
     * @return bool
     */
    public function isPublished(): bool
    {
        return (bool) $this->published;
    }

    /**
     * @return int
     */
    public function getStart(): int
    {
        return (int) $this->start;
    }

    /**
     * @return int
     */
    public function getStop(): int
    {
        return (int) $this->stop;
    }

    /**
     * Get the ID of the jump-to-page of the archive.
     *
     * @return int
     */
    public function getJumpToPageId(): int
    {
        $archive = JobArchiveModel::findById($this->getPid());
        if (null === $archive) {
            throw new AccessDeniedException('Invalid job-archive ID "' . $this->getPid() . '".');
        }

        return $archive->getJumpTo();
    }
}
