<?php

declare(strict_types=1);

/*
 * This file is part of the job posting bundle.
 *
 * @author     Thomas Dirscherl <https://github.com/xirdion>
 * @link       https://github.com/xirdion/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\EventListener;

use Contao\CoreBundle\Exception\ResponseException;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Database;
use Contao\DataContainer;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Contao\Widget;
use Dreibein\JobpostingBundle\Widget\JobCategoryPickerWidget;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Hook("executePostActions")
 *
 * @see \Contao\Ajax::executePostActions()
 */
class ExecutePostActionsListener
{
    private ContaoFramework $framework;

    /**
     * @param ContaoFramework $framework
     */
    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Handle the ajax post action from the custom picker-widget.
     *
     * @param string        $action
     * @param DataContainer $dc
     */
    public function __invoke(string $action, DataContainer $dc): void
    {
        // The actions are from the different picker-widgets
        switch ($action) {
            case 'reloadJobWidget':
                $this->handlePostAction($GLOBALS['BE_FFL']['jobPicker'], $dc);
                break;
            case 'reloadJobCategoryWidget':
                $this->handlePostAction($GLOBALS['BE_FFL']['jobCategoryPicker'], $dc);
                break;
        }
    }

    /**
     * @param string|Widget $widgetClass
     * @param DataContainer $dc
     */
    private function handlePostAction(string $widgetClass, DataContainer $dc): void
    {
        // Get the id and field from the request
        $db = $this->framework->createInstance(Database::class);
        $input = $this->framework->getAdapter(Input::class);
        $id = $input->get('id');
        $field = $dc->inputName = $input->post('name');

        // Handle the keys in "edit multiple" mode
        if ('editAll' === $input->get('act')) {
            $id = preg_replace('/.*_([0-9a-zA-Z]+)$/', '$1', $field);
            $field = preg_replace('/(.*)_[0-9a-zA-Z]+$/', '$1', $field);
        }
        $dc->field = $field;

        // The field does not exist
        if (!isset($GLOBALS['TL_DCA'][$dc->table]['fields'][$field])) {
            throw new BadRequestHttpException('Invalid field name: ' . $field);
        }

        // Load the value
        $value = null;
        if ('overrideAll' !== $input->get('act') && $id > 0 && $db->tableExists($dc->table)) {
            $row = $db->prepare('SELECT * FROM ' . $dc->table . ' WHERE id=?')->execute($id);

            // The record does not exist
            if ($row->numRows < 1) {
                throw new BadRequestHttpException('Bad request');
            }

            // The old value from the database
            $value = $row->$field;
            $dc->activeRecord = $row;
        }

        // Call the load_callback
        if (\is_array($GLOBALS['TL_DCA'][$dc->table]['fields'][$field]['load_callback'])) {
            $systemAdapter = $this->framework->getAdapter(System::class);

            foreach ($GLOBALS['TL_DCA'][$dc->table]['fields'][$field]['load_callback'] as $callback) {
                if (\is_array($callback)) {
                    $value = $systemAdapter->importStatic($callback[0])->{$callback[1]}($value, $dc);
                } elseif (\is_callable($callback)) {
                    $value = $callback($value, $dc);
                }
            }
        }

        // Set the new value
        $value = $input->post('value', true);

        // Convert the selected values
        if ($value) {
            $stringUtilAdapter = $this->framework->getAdapter(StringUtil::class);
            $value = $stringUtilAdapter->trimsplit("\t", $value);
            $value = serialize($value);
        }

        /** @var JobCategoryPickerWidget $objWidget */
        $widgetInstance = new $widgetClass($widgetClass::getAttributesFromDca($GLOBALS['TL_DCA'][$dc->table]['fields'][$field], $dc->inputName, $value, $field, $dc->table, $dc));

        throw new ResponseException(new Response($widgetInstance->generate()));
    }
}
