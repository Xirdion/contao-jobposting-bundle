<?php

declare(strict_types=1);

/*
 * This file is part of the job posting bundle.
 *
 * @author     Thomas Dirscherl <https://github.com/xirdion>
 * @link       https://github.com/xirdion/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\Model;

interface JobModelInterface
{
    /**
     * Get the correct title, if possible the frontend-title.
     *
     * @return string
     */
    public function getFrontendTitle(): string;
}
