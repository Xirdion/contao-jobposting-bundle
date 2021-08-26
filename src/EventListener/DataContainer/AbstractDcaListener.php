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

use Contao\BackendUser;
use Contao\Controller;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Contao\Model;
use Contao\StringUtil;
use Dreibein\JobpostingBundle\Job\AliasGenerator;
use Dreibein\JobpostingBundle\Repository\AliasInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

abstract class AbstractDcaListener
{
    protected AliasGenerator $aliasGenerator;

    public function __construct(AliasGenerator $aliasGenerator)
    {
        $this->aliasGenerator = $aliasGenerator;
    }

    /**
     * Generate a valid alias for this entry or show an error message.
     * The method is triggered when saving the alias field.
     *
     * @Callback(table="tl_job", target="fields.alias.save")
     * @Callback(table="tl_job_category", target="fields.alias.save")
     *
     * @param string        $newAlias
     * @param DataContainer $dc
     *
     * @throws \Exception
     *
     * @return string
     */
    public function generateAlias(string $newAlias, DataContainer $dc): string
    {
        /** @var AliasInterface $modelClass */
        $modelClass = Model::getClassFromTable($dc->table);

        // If no alias is given, just create a new one
        if (!$newAlias) {
            $job = $modelClass::findById((int) $dc->id);
            if (null === $job) {
                throw new \Contao\CoreBundle\Exception\AccessDeniedException('Invalid job ID "' . $dc->id . '".');
            }

            $newAlias = $this->aliasGenerator->generateAlias($job, $dc->activeRecord->title);
        } elseif (preg_match('/^[1-9]\d*$/', $newAlias)) {
            throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasNumeric'], $newAlias));
        } elseif ($modelClass::checkAlias((int) $dc->id, $newAlias) > 0) {
            throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $newAlias));
        }

        return $newAlias;
    }

    /**
     * @param string      $table
     * @param array       $record
     * @param string|null $href
     * @param string      $label
     * @param string      $title
     * @param string|null $icon
     * @param string      $attributes
     *
     * @return string
     */
    public function setToggleButton(string $table, array $record, ?string $href, string $label, string $title, ?string $icon, string $attributes): string
    {
        if (Input::get('tid')) {
            $this->toggleVisibility($table, (int) Input::get('tid'), (1 === (int) Input::get('state')));
            Controller::redirect(Controller::getReferer());
        }

        // Check permissions AFTER checking the tid, so hacking attempts are logged
        $user = BackendUser::getInstance();
        if (!$user->hasAccess($table . '::published', 'alexf')) {
            return '';
        }

        $href .= '&amp;tid=' . $record['id'] . '&amp;state=' . ($record['published'] ? '' : 1);

        if (!$record['published']) {
            $icon = 'invisible.svg';
        }

        return '<a href="' . Controller::addToUrl($href) . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label, 'data-state="' . ($record['published'] ? 1 : 0) . '"') . '</a> ';
    }

    /**
     * Update the published field for the given job category.
     *
     * @param string $table
     * @param int    $id
     * @param bool   $published
     */
    private function toggleVisibility(string $table, int $id, bool $published): void
    {
        // Set the ID and action
        Input::setGet('id', $id);
        Input::setGet('act', 'toggle');

        // Check the field access
        $user = BackendUser::getInstance();
        if (!$user->hasAccess($table . '::published', 'alexf')) {
            throw new AccessDeniedException('Not enough permissions to publish/unpublish ' . $table . ' ID "' . $id . '".');
        }

        // Check if the category could get loaded
        /** @var Model $modelClass */
        $modelClass = Model::getClassFromTable($table);
        $model = $modelClass::findById($id);
        if (null === $model) {
            throw new AccessDeniedException('Invalid ' . $table . ' ID "' . $id . '".');
        }

        // Update the database
        $model->tstamp = time();
        $model->published = $published;
        $model->save();
    }
}
