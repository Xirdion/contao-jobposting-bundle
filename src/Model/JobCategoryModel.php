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

use Dreibein\JobpostingBundle\Repository\JobCategoryRepository;

/**
 * Class JobCategoryModel.
 *
 * @property int    $tstamp
 * @property string $title
 * @property string $frontendTitle
 * @property string $alias
 * @property string $cssClass
 * @property string $description
 * @property int    $jumpTo
 * @property bool   $published
 * @property string $singleSRC
 */
class JobCategoryModel extends JobCategoryRepository
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
        return $this->frontendTitle ?: $this->title;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getCssClass(): string
    {
        return $this->cssClass;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getJumpTo(): int
    {
        return (int) $this->jumpTo;
    }

    /**
     * @return bool
     */
    public function isPublished(): bool
    {
        return (bool) $this->published;
    }

    /**
     * @return string|null
     */
    public function getSingleSRC(): ?string
    {
        return $this->singleSRC;
    }

    /**
     * Just use the ID of the jump-to-page of this category.
     *
     * @return int
     */
    public function getJumpToPageId(): int
    {
        return $this->getJumpTo();
    }
}
