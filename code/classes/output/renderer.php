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
 * The code question renderer class is defined here.
 *
 * @package     qtype_code
 * @copyright   2023 Stefan Wagner
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_availability\tree;
use tool_usertours\output\step;

defined('MOODLE_INTERNAL') || die();

/**
 * Generates the output for code questions.
 *
 * You should override functions as necessary from the parent class located at
 * /question/type/rendererbase.php.
 */
class qtype_code_renderer extends qtype_renderer {

    /**
     * Generates the display of the formulation part of the question. This is the
     * area that contains the quetsion text, and the controls for students to
     * input their answers. Some question types also embed bits of feedback, for
     * example ticks and crosses, in this area.
     *
     * @param question_attempt $qa the question attempt to display.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     */
    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        global $CFG;
        $question = $qa->get_question();

        $responseoutput = new qtype_code_monaco_renderer($this->page, RENDERER_TARGET_GENERAL);
        $responseoutput->set_displayoptions($options);

        $step = $qa->get_last_step_with_qt_var('answer');
        if (!$step->has_qt_var('answer') && empty($options->readonly)) {
            // Question has never been answered, fill it with response template.
            $step = new question_attempt_step(array('answer' => $question->responsetemplate));
        }

        if (empty($options->readonly)) {
            $answer = $responseoutput->response_area_input('answer', $qa, $step, $options->context);
            $edit = true;
        } else {
            $answer = $responseoutput->response_area_read_only('answer', $qa,
                    $step, $options->context);
            $edit = false;
        }

        $result = '';
        $result .= html_writer::tag('div', $question->format_questiontext($qa),
                array('class' => 'qtext'));

        $result .= html_writer::start_tag('div', array('class' => 'ablock'));
        $result .= html_writer::tag('div', $answer, array('class' => 'answer'));
        $result .= html_writer::end_tag('div');
        $result .= html_writer::tag('button', get_string('format', 'qtype_code'), array('id' => 'formater', 'type' => 'button'));

        if ($qa->get_question()->intel == 1) {
            $intel = true;
            if ($qa->get_question()->inline == 1) {
                $inline = true;
            } else {
                $inline = false;
            }

            if ($qa->get_question()->keywords == 1) {
                $keywords = true;
            } else {
                $keywords = false;
            }

            if ($qa->get_question()->variables == 1) {
                $variables = true;
            } else {
                $variables = false;
            }

            if ($qa->get_question()->functions == 1) {
                $functions = true;
            } else {
                $functions = false;
            }

            if ($qa->get_question()->classes == 1) {
                $classes = true;
            } else {
                $classes = false;
            }

            if ($qa->get_question()->modules == 1) {
                $modules = true;
            } else {
                $modules = false;
            }
        } else {
            $intel = false;
            $inline = false;
            $keywords = false;
            $variables = false;
            $functions = false;
            $classes = false;
            $modules = false;
        }
        $inputname = $qa->get_qt_field_name('answer');
        $id = $inputname . '_id';
        $url1 = new moodle_url('/question/type/code/monaco-editor/min/vs/loader.js');
        $url2 = new moodle_url('/question/type/code/monaco-editor/min/vs');
        $this->page->requires->js_call_amd('qtype_code/monaco', 'init', [[
            'lang' => $qa->get_question()->language,
            'url1' => $url1->__toString(),
            'url2' => $url2->__toString(),
            'text' => s($step->get_qt_var('answer')),
            'mID' => $id,
            'edit' => $edit,
            'intel' => $intel,
            'inline' => $inline,
            'keywords' => $keywords,
            'variables' => $variables,
            'functions' => $functions,
            'classes' => $classes,
            'modules' => $modules,
            'tabsize' => $qa->get_question()->tabsize,
        ]]);

        return $result;
    }

}

/**
 * A base class to abstract out the differences between different type of response format.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_code_format_renderer_base extends plugin_renderer_base {

    /** @var question_display_options Question display options instance for any necessary information for rendering the question. */
    protected $displayoptions;

    /**
     * Question number setter.
     *
     * @param question_display_options $displayoptions
     */
    public function set_displayoptions(question_display_options $displayoptions): void {
        $this->displayoptions = $displayoptions;
    }

    /**
     * Render the students respone when the question is in read-only mode.
     * @param string $name the variable name this input edits.
     * @param question_attempt $qa the question attempt being display.
     * @param question_attempt_step $step the current step.
     * @param object $context the context teh output belongs to.
     * @return string html to display the response.
     */
    abstract public function response_area_read_only($name, question_attempt $qa,
            question_attempt_step $step, $context);

    /**
     * Render the students respone when the question is in read-only mode.
     * @param string $name the variable name this input edits.
     * @param question_attempt $qa the question attempt being display.
     * @param question_attempt_step $step the current step.
     * @param object $context the context teh output belongs to.
     * @return string html to display the response for editing.
     */
    abstract public function response_area_input($name, question_attempt $qa,
            question_attempt_step $step, $context);

    /**
     * The class name
     * @return string specific class name to add to the input element.
     */
    abstract protected function class_name();
}


/**
 * A class for printing the necessary html tags for the monaco editor.
 *
 */
class qtype_code_monaco_renderer extends qtype_code_format_renderer_base {

    /**
     * Generates textarea for input
     *
     * @param string $response response of student
     * @param int $lines number of lines the textarea should have
     * @param array $attributes different attributes for textarea
     * @return void
     */
    protected function textarea($response, $lines, $attributes) {
        $attributes['class'] = $this->class_name() . ' qtype_code_response form-control';
        $attributes['rows'] = $lines;
        $attributes['cols'] = 60;
        return html_writer::tag('textarea', s($response), $attributes);
    }


    /**
     * The class name
     * @return string the name of the class
     */
    protected function class_name() {
        return 'qtype_code_monaco';
    }


    /**
     * Render the students respone when the question is in read-only mode.
     * @param string $name the variable name this input edits.
     * @param question_attempt $qa the question attempt being display.
     * @param question_attempt_step $step the current step.
     * @param object $context the context the output belongs to.
     * @return string html to display the response.
     */
    public function response_area_read_only($name, $qa, $step, $context) {
        $id = $qa->get_qt_field_name($name) . '_id';

        $responselabel = $this->displayoptions->add_question_identifier_to_label(get_string('answertext', 'qtype_code'));
        $output = html_writer::tag('label', $responselabel, ['class' => 'sr-only', 'for' => $id]);
        $output .= html_writer::div(null, null,
            array('id' => 'containerMonaco'. $id, 'style' => 'width:auto;height:600px;border:1px solid grey'));
        return $output;
    }

    /**
     * Render the students respone.
     * @param string $name the variable name this input edits.
     * @param question_attempt $qa the question attempt being display.
     * @param question_attempt_step $step the current step.
     * @param object $context the context the output belongs to.
     * @return string html to display the response for editing.
     */
    public function response_area_input($name, $qa, $step, $context) {
        $inputname = $qa->get_qt_field_name($name);
        $id = $inputname . '_id';

        $responselabel = $this->displayoptions->add_question_identifier_to_label(get_string('answertext', 'qtype_code'));
        $output = html_writer::tag('label', $responselabel, ['class' => 'sr-only', 'for' => $id]);
        $output .= $this->textarea($step->get_qt_var($name), 0, ['name' => $inputname, 'id' => $id, 'style' => 'display:none']);
        $output .= html_writer::div(null, null,
            array('id' => 'containerMonaco'. $id, 'style' => 'width:auto;height:600px;border:1px solid grey'));
        $output .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => $inputname . 'format', 'value' => FORMAT_PLAIN]);

        return $output;
    }

}
