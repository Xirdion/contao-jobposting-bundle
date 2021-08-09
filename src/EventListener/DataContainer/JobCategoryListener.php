<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job posting bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;

class JobCategoryListener extends AbstractDcaListener
{
    /**
     * @Callback(table="tl_job_category", target="list.operations.toggle.button")
     *
     * @param array       $record
     * @param string|null $href
     * @param string      $label
     * @param string      $title
     * @param string|null $icon
     * @param string      $attributes
     *
     * @return string
     */
    public function updateToggleButton(array $record, ?string $href, string $label, string $title, ?string $icon, string $attributes): string
    {
        return $this->setToggleButton('tl_job_category', $record, $href, $label, $title, $icon, $attributes);
    }
}
