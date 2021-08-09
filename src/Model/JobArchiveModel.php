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

use Dreibein\JobpostingBundle\Repository\JobArchiveRepository;

/**
 * Class JobArchiveModel.
 *
 * @property int    $tstamp
 * @property string $title
 * @property int    $jumpTo
 */
class JobArchiveModel extends JobArchiveRepository
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
     * @return int
     */
    public function getJumpTo(): int
    {
        return (int) $this->jumpTo;
    }
}
