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

use Contao\Controller;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\Database;
use Dreibein\JobpostingBundle\Model\JobArchiveModel;

class ModuleListener
{
    /**
     * Get all available job archives.
     *
     * @Callback(table="tl_module", target="fields.job_archives.options")
     *
     * @return array
     */
    public function getJobArchiveModules(): array
    {
        $archives = JobArchiveModel::findAll();
        if (null === $archives) {
            return [];
        }

        $data = [];
        foreach ($archives as $archive) {
            $data[$archive->getId()] = $archive->getTitle();
        }

        return $data;
    }

    /**
     * Find all job_reader modules in the tl_module table and group the by their theme name.
     *
     * @Callback(table="tl_module", target="fields.job_readerModule.options")
     *
     * @return array
     */
    public function getJobReaderModules(): array
    {
        $data = [];

        // Get all reader modules grouped by their theme name.
        $dataBase = Database::getInstance();
        $modules = $dataBase
            ->prepare('SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE m.type=? ORDER BY t.name, m.name')
            ->execute('job_reader')
        ;

        while ($modules->next()) {
            $data[$modules->theme][$modules->id] = $modules->name . ' (ID ' . $modules->id . ')';
        }

        return $data;
    }

    /**
     * Get all available job templates.
     *
     * @Callback(table="tl_module", target="fields.job_template.options")
     * @Callback(table="tl_content", target="fields.job_template.options")
     *
     * @return array
     */
    public function getJobTemplates(): array
    {
        return Controller::getTemplateGroup('job_');
    }

    /**
     * Get all the available sorting options.
     *
     * @Callback(table="tl_module", target="fields.job_order.options")
     *
     * @return string[]
     */
    public function getJobSortingOptions(): array
    {
        return ['order_date_asc', 'order_date_desc', 'order_title_asc', 'order_title_desc', 'order_random'];
    }
}
