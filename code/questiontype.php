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
            'abap' => get_string('languageabap', 'qtype_code'),
            'apex' => get_string('languageapex', 'qtype_code'),
            'azcli' => get_string('languageazcli', 'qtype_code'),
            'bat' => get_string('languagebat', 'qtype_code'),
            'bicep' => get_string('languagebicep', 'qtype_code'),
            'cameligo' => get_string('languagecameligo', 'qtype_code'),
            'clojure' => get_string('languageclojure', 'qtype_code'),
            'coffeescript' => get_string('languagecoffeescript', 'qtype_code'),
            'c' => get_string('languagec', 'qtype_code'),
            'cpp' => get_string('languagecpp', 'qtype_code'),
            'chsarp' => get_string('languagechsarp', 'qtype_code'),
            'csp' => get_string('languagecsp', 'qtype_code'),
            'css' => get_string('languagecss', 'qtype_code'),
            'cypher' => get_string('languagecypher', 'qtype_code'),
            'dart' => get_string('languagedart', 'qtype_code'),
            'dockerfile' => get_string('languagedockerfile', 'qtype_code'),
            'ecl' => get_string('languageecl', 'qtype_code'),
            'elixir' => get_string('languageelixir', 'qtype_code'),
            'flow9' => get_string('languageflow9', 'qtype_code'),
            'fsharp' => get_string('languagefsharp', 'qtype_code'),
            'freemarker2' => get_string('languagefreemarker2', 'qtype_code'),
            'freemarker2.tag-angle.interpolation-dollar'
                => get_string('languagefreemarker2.tag-angle.interpolation-dollar', 'qtype_code'),
            'freemarker2.tag-bracket.interpolation-dollar'
                => get_string('languagefreemarker2.tag-bracket.interpolation-dollar', 'qtype_code'),
            'freemarker2.tag-angle.interpolation-bracket'
                => get_string('languagefreemarker2.tag-angle.interpolation-bracket', 'qtype_code'),
            'freemarker2.tag-bracket.interpolation-bracket'
                => get_string('languagefreemarker2.tag-bracket.interpolation-bracket', 'qtype_code'),
            'freemarker2.tag-auto.interpolation-dollar'
                => get_string('languagefreemarker2.tag-auto.interpolation-dollar', 'qtype_code'),
            'freemarker2.tag-auto.interpolation-bracket'
                => get_string('languagefreemarker2.tag-auto.interpolation-bracket', 'qtype_code'),
            'go' => get_string('languagego', 'qtype_code'),
            'graphql' => get_string('languagegraphql', 'qtype_code'),
            'handlebars' => get_string('languagehandlebars', 'qtype_code'),
            'hcl' => get_string('languagehcl', 'qtype_code'),
            'html' => get_string('languagehtml', 'qtype_code'),
            'ini' => get_string('languageini', 'qtype_code'),
            'java' => get_string('languagejava', 'qtype_code'),
            'javascript' => get_string('languagejavascript', 'qtype_code'),
            'julia' => get_string('languagejulia', 'qtype_code'),
            'kotlin' => get_string('languagekotlin', 'qtype_code'),
            'less' => get_string('languageless', 'qtype_code'),
            'lexon' => get_string('languagelexon', 'qtype_code'),
            'lua' => get_string('languagelua', 'qtype_code'),
            'liquid' => get_string('languageliquid', 'qtype_code'),
            'm3' => get_string('languagem3', 'qtype_code'),
            'markdown' => get_string('languagemarkdown', 'qtype_code'),
            'mips' => get_string('languagemips', 'qtype_code'),
            'msdax' => get_string('languagemsdax', 'qtype_code'),
            'mysql' => get_string('languagemysql', 'qtype_code'),
            'objective-c' => get_string('languageobjective-c', 'qtype_code'),
            'pascal' => get_string('languagepascal', 'qtype_code'),
            'pascaligo' => get_string('languagepascaligo', 'qtype_code'),
            'perl' => get_string('languageperl', 'qtype_code'),
            'pgsql' => get_string('languagepgsql', 'qtype_code'),
            'php' => get_string('languagephp', 'qtype_code'),
            'pla' => get_string('languagepla', 'qtype_code'),
            'postiats' => get_string('languagepostiats', 'qtype_code'),
            'powerquery' => get_string('languagepowerquery', 'qtype_code'),
            'powershell' => get_string('languagepowershell', 'qtype_code'),
            'proto' => get_string('languageproto', 'qtype_code'),
            'pug' => get_string('languagepug', 'qtype_code'),
            'python' => get_string('languagepython', 'qtype_code'),
            'qsharp' => get_string('languageqsharp', 'qtype_code'),
            'r' => get_string('languager', 'qtype_code'),
            'razor' => get_string('languagerazor', 'qtype_code'),
            'redis' => get_string('languageredis', 'qtype_code'),
            'redshift' => get_string('languageredshift', 'qtype_code'),
            'restructuredtext' => get_string('languagerestructuredtext', 'qtype_code'),
            'ruby' => get_string('languageruby', 'qtype_code'),
            'rust' => get_string('languagerust', 'qtype_code'),
            'sb' => get_string('languagesb', 'qtype_code'),
            'scala' => get_string('languagescala', 'qtype_code'),
            'scheme' => get_string('languagescheme', 'qtype_code'),
            'scss' => get_string('languagescss', 'qtype_code'),
            'shell' => get_string('languageshell', 'qtype_code'),
            'sol' => get_string('languagesol', 'qtype_code'),
            'aes' => get_string('languageaes', 'qtype_code'),
            'sparql' => get_string('languagesparql', 'qtype_code'),
            'sql' => get_string('languagesql', 'qtype_code'),
            'st' => get_string('languagest', 'qtype_code'),
            'swift' => get_string('languageswift', 'qtype_code'),
            'systemverilog' => get_string('languagesystemverilog', 'qtype_code'),
            'verilog' => get_string('languageverilog', 'qtype_code'),
            'tcl' => get_string('languagetcl', 'qtype_code'),
            'twig' => get_string('languagetwig', 'qtype_code'),
            'typescript' => get_string('languagetypescript', 'qtype_code'),
            'vb' => get_string('languagevb', 'qtype_code'),
            'wgsl' => get_string('languagewgsl', 'qtype_code'),
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
        $options->responsetemplate = $formdata->responsetemplate['text'];
        $DB->update_record('qtype_code_options', $options);
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
