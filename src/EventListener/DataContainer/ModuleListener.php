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
