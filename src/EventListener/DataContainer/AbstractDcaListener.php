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
use Contao\Image;
use Contao\Input;
use Contao\Model;
use Contao\StringUtil;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

abstract class AbstractDcaListener
{
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
