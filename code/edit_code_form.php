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

        $mform->addElement('select', 'language', get_string('languages', 'qtype_code'), $qtype->languages());
        $mform->setDefault('language', 'plaintext');

        $mform->addElement('select', 'intellisense', get_string('intellisense', 'qtype_code'), $qtype->intellisense());
        $mform->setDefault('intellisense', '2');

        $mform->addElement('header', 'responsetemplateheader', get_string('responsetemplateheader', 'qtype_code'));
        $mform->addElement('textarea', 'responsetemplate', get_string("responsetemplate", "qtype_code"),
            'wrap="virtual" rows="20" cols="50"');
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

        $question->intellisense = $question->options->intellisense;

        return $question;
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
