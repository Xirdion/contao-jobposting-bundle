<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job posting bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\Picker;

use Contao\CoreBundle\Picker\AbstractPickerProvider;
use Contao\CoreBundle\Picker\DcaPickerProviderInterface;
use Contao\CoreBundle\Picker\PickerConfig;
use Contao\CoreBundle\ServiceAnnotation\PickerProvider;

/**
 * @PickerProvider()
 */
class JobPickerProvider extends AbstractPickerProvider implements DcaPickerProviderInterface
{
    /**
     * Returns which DCA table this picker is for.
     *
     * @return string
     */
    public function getDcaTable(): string
    {
        return 'tl_job';
    }

    /**
     * Returns attributes for the dca picker.
     *
     * @param PickerConfig $config
     *
     * @return string[]
     */
    public function getDcaAttributes(PickerConfig $config): array
    {
        $attributes = ['fieldType' => 'text'];

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
     * Returns the name of the picker.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'jobPicker';
    }

    /**
     * Check if the context is supported for this picker.
     *
     * @param string $context
     *
     * @return bool
     */
    public function supportsContext($context): bool
    {
        return 'job' === $context;
    }

    /**
     * Check if the value is supported.
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
     * Get the route parameters for the picker.
     *
     * @param PickerConfig|null $config
     *
     * @return string[]
     */
    protected function getRouteParameters(PickerConfig $config = null): array
    {
        return ['do' => 'jobs'];
    }
}
