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

interface JobModelInterface
{
    /**
     * Get the correct title, if possible the frontend-title.
     *
     * @return string
     */
    public function getFrontendTitle(): string;
}
