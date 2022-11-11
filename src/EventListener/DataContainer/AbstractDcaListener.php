<?php

declare(strict_types=1);

/*
 * This file is part of the job posting bundle.
 *
 * @author     Thomas Dirscherl <https://github.com/xirdion>
 * @link       https://github.com/xirdion/contao-jobposting-bundle
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

    protected array $toggleData;

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

    public function setToggleData(string $field, string $act, string $table, string $icon, string $icon_inactive): void
    {
        $this->toggleData = [
            'field' => $field,
            'act' => $act,
            'table' => $table,
            'icon' => $icon,
            'icon_inactive' => $icon_inactive,
        ];
    }

    /**
     * Function to set the ulr and icon of the button. The button-action is also handled here.
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
    public function setToggleButton(array $record, ?string $href, string $label, string $title, ?string $icon, string $attributes): string
    {
        $field = $this->toggleData['field'];
        $table = $this->toggleData['table'];
        $actionId = $this->toggleData['act'][0] . 'id';

        if (Input::get($actionId)) {
            $this->toggleField((int) Input::get($actionId), 1 === (int) Input::get('state'));
            Controller::redirect(Controller::getReferer());
        }

        // Check permissions AFTER checking the action-id, so hacking attempts are logged
        $user = BackendUser::getInstance();
        if (!$user->hasAccess($table . '::' . $field, 'alexf')) {
            return '';
        }

        $href .= '&amp;' . $actionId . '=' . $record['id'] . '&amp;state=' . ($record[$field] ? '' : 1);

        $btnIcon = !$record[$field] ? $this->toggleData['icon_inactive'] : $this->toggleData['icon'];

        return '<a href="' . Controller::addToUrl($href) . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . Image::getHtml($btnIcon, $label, 'data-state="' . ($record[$field] ? 1 : 0) . '"') . '</a> ';
    }

    /**
     * Update the field value (boolean).
     *
     * @param int  $id
     * @param bool $toggled
     */
    private function toggleField(int $id, bool $toggled): void
    {
        $field = $this->toggleData['field'];
        $table = $this->toggleData['table'];

        // Set the ID and action
        Input::setGet('id', $id);
        Input::setGet('act', $this->toggleData['act']);

        // Check the field access
        $user = BackendUser::getInstance();
        if (!$user->hasAccess($table . '::' . $field, 'alexf')) {
            throw new AccessDeniedException('Not enough permissions to update ' . $table . ' ID "' . $id . '".');
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
        $model->{$field} = (int) $toggled;
        $model->save();
    }
}
