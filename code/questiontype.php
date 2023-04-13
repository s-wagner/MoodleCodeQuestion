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
 * Question type class for code is defined here.
 *
 * @package     qtype_code
 * @copyright   2023 Stefan Wagner
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/questionlib.php');

/**
 * Class that represents a code question type.
 *
 * The class loads, saves and deletes questions of the type code
 * to and from the database and provides methods to help with editing questions
 * of this type. It can also provide the implementation for import and export
 * in various formats.
 */
class qtype_code extends question_type {

    // Override functions as necessary from the parent class located at
    // /question/type/questiontype.php.

    public function is_manual_graded() {
        return true;
    }

    public function languages() {
        return array(
            'plaintext' => get_string('languageplaintext', 'qtype_code'),
            'c' => get_string('languagec', 'qtype_code'),
            'cpp' => get_string('languagecpp', 'qtype_code'),
            'csharp' => get_string('languagechsarp', 'qtype_code'),
            'css' => get_string('languagecss', 'qtype_code'),
            'dockerfile' => get_string('languagedockerfile', 'qtype_code'),
            'html' => get_string('languagehtml', 'qtype_code'),
            'java' => get_string('languagejava', 'qtype_code'),
            'javascript' => get_string('languagejavascript', 'qtype_code'),
            'julia' => get_string('languagejulia', 'qtype_code'),
            'kotlin' => get_string('languagekotlin', 'qtype_code'),
            'pascal' => get_string('languagepascal', 'qtype_code'),
            'perl' => get_string('languageperl', 'qtype_code'),
            'php' => get_string('languagephp', 'qtype_code'),
            'powershell' => get_string('languagepowershell', 'qtype_code'),
            'python' => get_string('languagepython', 'qtype_code'),
            'r' => get_string('languager', 'qtype_code'),
            'razor' => get_string('languagerazor', 'qtype_code'),
            'ruby' => get_string('languageruby', 'qtype_code'),
            'rust' => get_string('languagerust', 'qtype_code'),
            'shell' => get_string('languageshell', 'qtype_code'),
            'sql' => get_string('languagesql', 'qtype_code'),
            'swift' => get_string('languageswift', 'qtype_code'),
            'typescript' => get_string('languagetypescript', 'qtype_code'),
            'vb' => get_string('languagevb', 'qtype_code'),
            'xml' => get_string('languagexml', 'qtype_code'),
            'yaml' => get_string('languageyaml', 'qtype_code'),
            'json' => get_string('languagejson', 'qtype_code')
        );
    }

    public function get_question_options($question) {
        global $DB;
        $question->options = $DB->get_record('qtype_code_options',
                array('questionid' => $question->id), '*', MUST_EXIST);
        parent::get_question_options($question);
    }

    public function save_defaults_for_new_questions(stdClass $fromform): void {
        parent::save_defaults_for_new_questions($fromform);
        $this->set_default_value('language', 'plaintext');
    }

    public function save_question_options($formdata) {
        global $DB;
        $context = $formdata->context;

        $options = $DB->get_record('qtype_code_options', array('questionid' => $formdata->id));
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $formdata->id;
            $options->language = '';
            $options->id = $DB->insert_record('qtype_code_options', $options);
        }

        $options->language = $formdata->language;
        $options->responsetemplate = $formdata->responsetemplate;
        $DB->update_record('qtype_code_options', $options);
    }

    public function response_file_areas() {
        return array('answer');
    }


    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $question->language = $questiondata->options->language;
        $question->responsetemplate = $questiondata->options->responsetemplate;
    }

    public function delete_question($questionid, $contextid) {
        global $DB;

        $DB->delete_records('qtype_code_options', array('questionid' => $questionid));
        parent::delete_question($questionid, $contextid);
    }

}
