<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job posting bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\Widget;

use Contao\System;
use Contao\Widget;
use Dreibein\JobpostingBundle\Model\JobCategoryModel;

/**
 * Custom picker widget to be used in a dca file.
 *
 * @see \Contao\Picker
 */
class JobCategoryPickerWidget extends Widget
{
    protected $blnSubmitInput = true;
    protected $strTemplate = 'be_widget';

    /**
     * Generate the HTML for the custom job-category-picker-widget.
     *
     * @see \Contao\PageTree::generate()
     *
     * @throws \Exception
     *
     * @return string
     */
    public function generate(): string
    {
        // Collect the selected values
        $values = [];
        if (false === empty($this->varValue)) {
            $categories = JobCategoryModel::findByIds((array) $this->varValue);
            if (null !== $categories) {
                foreach ($categories as $category) {
                    $values[$category->getId()] = $category->getTitle();
                }
            }
        }

        // Generate widget html
        $return = '<input type="hidden" name="' . $this->strName . '" id="ctrl_' . $this->strId . '" value="' . implode(',', array_keys($values)) . '"><div class="selector_container"><ul id="sort_' . $this->strId . '">';

        // Add the selected values to the html
        foreach ($values as $k => $v) {
            $return .= '<li data-id="' . $k . '">' . $v . '</li>';
        }

        $return .= '</ul>';

        $pickerBuilder = System::getContainer()->get('contao.picker.builder');
        if (null === $pickerBuilder) {
            throw new \Exception('Something went wrong!');
        }

        if (!$pickerBuilder->supportsContext('jobCategory')) {
            $return .= '
	<p><button class="tl_submit" disabled>' . $GLOBALS['TL_LANG']['MSC']['changeSelection'] . '</button></p>';
        } else {
            $extras = ['fieldType' => $this->fieldType];

            $return .= '
	<p><a href="' . ampersand($pickerBuilder->getUrl('jobCategory', $extras)) . '" class="tl_submit" id="pt_' . $this->strName . '">' . $GLOBALS['TL_LANG']['MSC']['changeSelection'] . '</a></p>
	<script>
	  $("pt_' . $this->strName . '").addEvent("click", function(e) {
		e.preventDefault();
		Backend.openModalSelector({
		  "id": "tl_listing",
		  "title": ' . json_encode($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['label'][0]) . ',
		  "url": this.href + document.getElementById("ctrl_' . $this->strId . '").value,
		  "callback": function(table, value) {
			new Request.Contao({
			  evalScripts: false,
			  onSuccess: function(txt, json) {
				$("ctrl_' . $this->strId . '").getParent("div").set("html", json.content);
				json.javascript && Browser.exec(json.javascript);
				$("ctrl_' . $this->strId . '").fireEvent("change");
			  }
			}).post({"action":"reloadJobCategoryWidget", "name":"' . $this->strName . '", "value":value.join("\t"), "REQUEST_TOKEN":"' . REQUEST_TOKEN . '"});
		  }
		});
	  });
	</script>';
        }

        $return = '<div>' . $return . '</div></div>';

        return $return;
    }

    /**
     * @param mixed $varInput
     *
     * @return mixed
     */
    protected function validator($varInput)
    {
        if ($this->hasErrors()) {
            return '';
        }

        // Return the value as usual
        if (!$varInput) {
            if ($this->mandatory) {
                $this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['mandatory'], $this->strLabel));
            }

            return '';
        }

        if (false === strpos($varInput, ',')) {
            return $this->multiple ? [(int) $varInput] : (int) $varInput;
        }

        $values = array_map('\intval', array_filter(explode(',', $varInput)));

        return $this->multiple ? $values : $values[0];
    }
}
