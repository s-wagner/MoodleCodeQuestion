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

    /**
     * Sets that this question is manual graded
     *
     * @return boolean
     */
    public function is_manual_graded() {
        return true;
    }

    /**
     * Defines all programming languages supported by this question type
     *
     * @return array
     */
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

    /**
     * Get options for specific code question
     *
     * @param question $question current question
     * @return object
     */
    public function get_question_options($question) {
        global $DB;
        $question->options = $DB->get_record('qtype_code_options',
                array('questionid' => $question->id), '*', MUST_EXIST);
        parent::get_question_options($question);
    }

    /**
     * Saves default option values for new question
     *
     * @param stdClass $fromform form object
     * @return void
     */
    public function save_defaults_for_new_questions(stdClass $fromform): void {
        parent::save_defaults_for_new_questions($fromform);
        $this->set_default_value('language', 'plaintext');
        $this->set_default_value('intellisense', '2');
    }

    /**
     * Saves option values for new question
     *
     * @param object $formdata form object
     * @return void
     */
    public function save_question_options($formdata) {
        global $DB;
        $context = $formdata->context;

        $options = $DB->get_record('qtype_code_options', array('questionid' => $formdata->id));
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $formdata->id;
            $options->language = '';
            $options->inline = 0;
            $options->keywords = 0;
            $options->variables = 0;
            $options->functions = 0;
            $options->classes = 0;
            $options->modules = 0;
            $options->tabsize = 4;
            $options->id = $DB->insert_record('qtype_code_options', $options);
        }

        $options->language = $formdata->language;
        $options->responsetemplate = $formdata->responsetemplate;
        $options->intellisense = $formdata->intellisense;
        $options->inline = $formdata->inline;
        $options->keywords = $formdata->keywords;
        $options->variables = $formdata->variables;
        $options->functions = $formdata->functions;
        $options->classes = $formdata->classes;
        $options->modules = $formdata->modules;
        $options->intel = $formdata->intel;
        $options->tabsize = $formdata->tabsize;
        $DB->update_record('qtype_code_options', $options);
    }

    /**
     * Defines all repsone fields
     *
     * @return array
     */
    public function response_file_areas() {
        return array('answer');
    }

    /**
     * Initialses a new question instance
     *
     * @param question_definition $question question definition
     * @param object $questiondata object contains question options
     * @return void
     */
    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $question->language = $questiondata->options->language;
        $question->responsetemplate = $questiondata->options->responsetemplate;
        $question->intellisense = $questiondata->options->intellisense;
        $question->intel = $questiondata->options->intel;
        $question->inline = $questiondata->options->inline;
        $question->keywords = $questiondata->options->keywords;
        $question->variables = $questiondata->options->variables;
        $question->functions = $questiondata->options->functions;
        $question->classes = $questiondata->options->classes;
        $question->modules = $questiondata->options->modules;
        $question->tabsize = $questiondata->options->tabsize;
    }

    /**
     * Deletes a question from the DB
     *
     * @param int $questionid id of question to be deleted
     * @param int $contextid the context this quesiotn belongs to.
     * @return void
     */
    public function delete_question($questionid, $contextid) {
        global $DB;

        $DB->delete_records('qtype_code_options', array('questionid' => $questionid));
        parent::delete_question($questionid, $contextid);
    }

}
