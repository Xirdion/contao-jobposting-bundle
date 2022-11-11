<?php

declare(strict_types=1);

/*
 * This file is part of the job posting bundle.
 *
 * @author     Thomas Dirscherl <https://github.com/xirdion>
 * @link       https://github.com/xirdion/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\Model;

use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\StringUtil;
use Dreibein\JobpostingBundle\Repository\JobRepository;

/**
 * Class JobModel.
 *
 * @property int     $pid
 * @property int     $tstamp
 * @property string  $title
 * @property string  $alias
 * @property string  $categories
 * @property int     $date
 * @property int     $time
 * @property string  $pageTitle
 * @property ?string $description
 * @property string  $teaser
 * @property string  $company
 * @property string  $companyUrl
 * @property string  $companyLogo
 * @property string  $job_type
 * @property string  $job_times
 * @property string  $postal
 * @property string  $city
 * @property string  $street
 * @property string  $region
 * @property string  $country
 * @property bool    $remote
 * @property string  $salary
 * @property string  $salaryInterval
 * @property string  $responsibility
 * @property string  $skills
 * @property string  $qualification
 * @property string  $education
 * @property string  $experience
 * @property bool    $addImage
 * @property string  $singleSRC
 * @property string  $size
 * @property string  $floating
 * @property string  $imagemargin
 * @property bool    $fullsize
 * @property bool    $overwriteMeta
 * @property string  $alt
 * @property string  $imageTitle
 * @property string  $imageUrl
 * @property string  $caption
 * @property bool    $apply_active
 * @property string  $apply_link
 * @property string  $apply_inactive_link
 * @property ?string $apply_inactive_text
 * @property string  $cssClass
 * @property bool    $featured
 * @property bool    $published
 * @property int     $start
 * @property int     $stop
 */
class JobModel extends JobRepository implements JobModelInterface
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
    public function getFrontendTitle(): string
    {
        return $this->getTitle();
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
    public function getDate(): int
    {
        return (int) $this->date;
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        return (int) $this->time;
    }

    /**
     * @return string
     */
    public function getPageTitle(): string
    {
        return $this->pageTitle;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
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
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function getCompanyUrl(): string
    {
        return $this->companyUrl;
    }

    /**
     * @return string
     */
    public function getCompanyLogo(): string
    {
        return (string) $this->companyLogo;
    }

    /**
     * @return array
     */
    public function getJobType(): array
    {
        return StringUtil::deserialize($this->job_type, true);
    }

    /**
     * @return string
     */
    public function getJobTimes(): string
    {
        return $this->job_times;
    }

    /**
     * @return string
     */
    public function getPostal(): string
    {
        return $this->postal;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return bool
     */
    public function isRemote(): bool
    {
        return (bool) $this->remote;
    }

    /**
     * @return float
     */
    public function getSalary(): float
    {
        return (float) $this->salary;
    }

    /**
     * @return string
     */
    public function getSalaryInterval(): string
    {
        return $this->salaryInterval;
    }

    /**
     * @return string
     */
    public function getResponsibility(): string
    {
        return $this->responsibility;
    }

    /**
     * @return string
     */
    public function getSkills(): string
    {
        return $this->skills;
    }

    /**
     * @return string
     */
    public function getQualification(): string
    {
        return $this->qualification;
    }

    /**
     * @return string
     */
    public function getEducation(): string
    {
        return $this->education;
    }

    /**
     * @return string
     */
    public function getExperience(): string
    {
        return $this->experience;
    }

    /**
     * @return bool
     */
    public function isApplyActive(): bool
    {
        return (bool) $this->apply_active;
    }

    /**
     * @return string
     */
    public function getApplyLink(): string
    {
        return $this->apply_link;
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

    /**
     * Check if the current job is actively accessible.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        $time = time();

        return !(false === $this->isPublished() || ($this->getStart() && $this->getStart() > $time) || ($this->getStop() && $this->getStop() <= $time));
    }
}
