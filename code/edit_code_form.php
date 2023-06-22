<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * The editing form for code question type is defined here.
 *
 * @package     qtype_code
 * @copyright   2023 Stefan Wagner
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * code question editing form defition.
 *
 */
class qtype_code_edit_form extends question_edit_form {

    /**
     * Generates the input form for qtype_code options
     *
     * @param object $mform form object
     * @return void
     */
    protected function definition_inner($mform) {
        $qtype = question_bank::get_qtype('code');

        $mform->addElement('header', 'responseoptions', get_string('responseoptions', 'qtype_code'));
        $mform->setExpanded('responseoptions');

        $mform->addElement('select', 'language', get_string('languages', 'qtype_code'), $qtype->languages()); //selection for different programming languages
        $mform->setDefault('language', 'plaintext');

        $mform->addElement('advcheckbox', 'intel', get_string('intel', 'qtype_code'), //checkbox to enable autocomplete
            '', null, array(0, 1));

        $mform->addElement('advcheckbox', 'inline', get_string('enableinline', 'qtype_code'), //checkbox to enable inline documentation
            '', null, array(0, 1));
        $mform->addElement('advcheckbox', 'keywords', get_string('enablekeywords', 'qtype_code'), //checkbox to enable keyword autocomplete
            '', null, array(0, 1));
        $mform->addElement('advcheckbox', 'variables', get_string('enablevars', 'qtype_code'), //checkbox to enable variable autocomplete
            '', null, array(0, 1));
        $mform->addElement('advcheckbox', 'functions', get_string('enablefunctions', 'qtype_code'), //checkbox to enable function autocomplete
            '', null, array(0, 1));
        $mform->addElement('advcheckbox', 'classes', get_string('enableclasses', 'qtype_code'), //checkbox to enable class autocomplete
            '', null, array(0, 1));
        $mform->addElement('advcheckbox', 'modules', get_string('enablemodules', 'qtype_code'), //checkbox to enable module autocomplete
            '', null, array(0, 1));

        //disables all autocomplete checkboxes if autocomplete is disabled
        $mform->disabledIf('inline', 'intel', 'eq', '0');  
        $mform->disabledIf('keywords', 'intel', 'eq', '0');
        $mform->disabledIf('variables', 'intel', 'eq', '0');
        $mform->disabledIf('functions', 'intel', 'eq', '0');
        $mform->disabledIf('classes', 'intel', 'eq', '0');
        $mform->disabledIf('modules', 'intel', 'eq', '0');

        $tabsizeoptions = ['size' => '1', 'maxlength' => '1'];
        $mform->addElement('text', 'tabsize', get_string('tabsize', 'qtype_code'), $tabsizeoptions); //set the tabulator size
        $mform->setType('tabsize', PARAM_TEXT);
        $mform->setDefault('tabsize', '2');

        $mform->addElement('header', 'responsetemplateheader', get_string('responsetemplateheader', 'qtype_code'));
        $mform->addElement('textarea', 'responsetemplate', get_string("responsetemplate", "qtype_code"), //set the response template
            'wrap="virtual" rows="20" cols="50" style="font-family: monospace, monospace;"');
        $mform->setType('responsetemplate', PARAM_TEXT);
        $mform->addHelpButton('responsetemplate', 'responsetemplate', 'qtype_code');
    }

    /**
     * Processes input form data
     *
     * @param question $question new question
     * @return question
     */
    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);

        if (empty($question->options)) {
            return $question;
        }

        $question->language = $question->options->language;

        $question->responsetemplate = $question->options->responsetemplate;

        $question->intel = $question->options->intel;

        $question->inline = $question->options->inline;

        $question->keywords = $question->options->keywords;

        $question->variables = $question->options->variables;

        $question->functions = $question->options->functions;

        $question->classes = $question->options->classes;

        $question->modules = $question->options->modules;

        $question->tabsize = $question->options->tabsize;

        return $question;
    }

    /**
     * Validates form input
     *
     * @param object $fromform form object
     * @param object $files input files
     * @return array errors
     */
    public function validation($fromform, $files) {
        $errors = parent::validation($fromform, $files);

        //checks if the tabsize is numeric and above 0
        if (!is_numeric($fromform['tabsize'])) { 
            $errors['tabsize'] = get_string('err_numeric', 'form');
        } else if ($fromform['tabsize'] <= 0) {
            $errors['tabsize'] = get_string('err_tabsizenegative', 'qtype_code');
        }

        return $errors;
    }

    /**
     * Returns the question type name.
     *
     * @return string The question type name.
     */
    public function qtype() {
        return 'code';
    }


}
