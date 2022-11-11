<?php

declare(strict_types=1);

/*
 * This file is part of the job posting bundle.
 *
 * @author     Thomas Dirscherl <https://github.com/xirdion>
 * @link       https://github.com/xirdion/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\Picker;

use Contao\CoreBundle\Picker\AbstractPickerProvider;
use Contao\CoreBundle\Picker\DcaPickerProviderInterface;
use Contao\CoreBundle\Picker\PickerConfig;
use Contao\CoreBundle\ServiceAnnotation\PickerProvider;

/**
 * @PickerProvider()
 *
 * @see \Contao\CoreBundle\Picker\PagePickerProvider
 */
class JobCategoryPickerProvider extends AbstractPickerProvider implements DcaPickerProviderInterface
{
    /**
     * Get the individual name of the picker.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'jobCategoryPicker';
    }

    /**
     * Check if the picker is supported in the given context.
     *
     * @param string $context
     *
     * @return bool
     */
    public function supportsContext($context): bool
    {
        return 'jobCategory' === $context;
    }

    /**
     * Check if the given value is the numeric ID of a job category.
     *
     * @param PickerConfig $config
     *
     * @return bool
     */
    public function supportsValue(PickerConfig $config): bool
    {
        foreach (explode(',', $config->getValue()) as $id) {
            if (!is_numeric($id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the database table for the dca picker.
     *
     * @return string
     */
    public function getDcaTable(): string
    {
        return 'tl_job_category';
    }

    /**
     * Prepare some settings and attributes for viewing the dca.
     *
     * @param PickerConfig $config
     *
     * @return string[]
     */
    public function getDcaAttributes(PickerConfig $config): array
    {
        // Default fieldType is checkbox (multiple)
        $attributes = ['fieldType' => 'checkbox'];

        if ($fieldType = $config->getExtra('fieldType')) {
            $attributes['fieldType'] = $fieldType;
        }

        if ($this->supportsValue($config)) {
            $attributes['value'] = array_map('intval', explode(',', $config->getValue()));
        }

        return $attributes;
    }

    /**
     * @param PickerConfig $config
     * @param $value
     *
     * @return int
     */
    public function convertDcaValue(PickerConfig $config, $value): int
    {
        return (int) $value;
    }

    /**
     * Get a do-parameter for the route.
     *
     * @param PickerConfig|null $config
     *
     * @return string[]
     */
    protected function getRouteParameters(PickerConfig $config = null): array
    {
        // Adding the table to the route so the picker has the correct entry-level
        return ['do' => 'jobs', 'table' => 'tl_job_category'];
    }
}
