<?php

declare(strict_types=1);

/*
 * This file is part of the job posting bundle.
 *
 * @author     Thomas Dirscherl <https://github.com/xirdion>
 * @link       https://github.com/xirdion/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\Picker;

use Contao\CoreBundle\Picker\AbstractInsertTagPickerProvider;
use Contao\CoreBundle\Picker\DcaPickerProviderInterface;
use Contao\CoreBundle\Picker\PickerConfig;
use Contao\CoreBundle\ServiceAnnotation\PickerProvider;

/**
 * @PickerProvider()
 */
class JobPickerProvider extends AbstractInsertTagPickerProvider implements DcaPickerProviderInterface
{
    /**
     * Get the internal name of the picker.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'jobPicker';
    }

    /**
     * Only a specific context is support by this picker.
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
     * Check if a given value is supported by the picker.
     *
     * @param PickerConfig $config
     *
     * @return bool
     */
    public function supportsValue(PickerConfig $config): bool
    {
        if ('job' === $config->getContext()) {
            return is_numeric($config->getValue());
        }

        return $this->isMatchingInsertTag($config);
    }

    /**
     * Get the database table for the dca picker.
     *
     * @return string
     */
    public function getDcaTable(): string
    {
        return 'tl_job';
    }

    /**
     * Set some attributes for the picker.
     *
     * @param PickerConfig $config
     *
     * @return string[]
     */
    public function getDcaAttributes(PickerConfig $config): array
    {
        $value = $config->getValue();
        $attributes = ['fieldType' => 'radio'];

        if ('job' === $config->getContext()) {
            if ($fieldType = $config->getExtra('fieldType')) {
                $attributes['fieldType'] = $fieldType;
            }

            if ($source = $config->getExtra('source')) {
                $attributes['preserveRecord'] = $source;
            }

            if ($value) {
                $attributes['value'] = array_map('\intval', explode(',', $value));
            }

            return $attributes;
        }

        if ($value && $this->isMatchingInsertTag($config)) {
            $attributes['value'] = $this->getInsertTagValue($config);

            if ($flags = $this->getInsertTagFlags($config)) {
                $attributes['flags'] = $flags;
            }
        }

        return $attributes;
    }

    /**
     * Converting the given picker value to the correct id or insert-tag.
     *
     * @param PickerConfig $config
     * @param $value
     *
     * @return int|string
     */
    public function convertDcaValue(PickerConfig $config, $value)
    {
        if ('job' === $config->getContext()) {
            return (int) $value;
        }

        return sprintf($this->getInsertTag($config), $value);
    }

    /**
     * Add some specific route parameters.
     *
     * @param PickerConfig|null $config
     *
     * @return string[]
     */
    protected function getRouteParameters(PickerConfig $config = null): array
    {
        return ['do' => 'jobs'];
    }

    /**
     * Get the default insert tag for the picker value.
     *
     * @return string
     */
    protected function getDefaultInsertTag(): string
    {
        return '{{job_url::%s}}';
    }
}
