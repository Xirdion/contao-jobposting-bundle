<?php

declare(strict_types=1);

/*
 * This file is part of the job posting bundle.
 *
 * @author     Thomas Dirscherl <https://github.com/xirdion>
 * @link       https://github.com/xirdion/contao-jobposting-bundle
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
        $this->setToggleData('published', 'toggle', 'tl_job_category', 'visible.svg', 'invisible.svg');

        return $this->setToggleButton($record, $href, $label, $title, $icon, $attributes);
    }
}
