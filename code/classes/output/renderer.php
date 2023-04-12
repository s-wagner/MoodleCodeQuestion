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

use tool_usertours\output\step;

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

        } else {
            $answer = $responseoutput->response_area_read_only('answer', $qa,
                    $step, $options->context);
            
        }

        $result = '';
        $result .= html_writer::tag('div', $question->format_questiontext($qa),
                array('class' => 'qtext'));

        $result .= html_writer::start_tag('div', array('class' => 'ablock'));
        $result .= html_writer::tag('div', $answer, array('class' => 'answer'));
        $result .= html_writer::end_tag('div');
        return $result;
        //return parent::formulation_and_controls($qa, $options);
    }

   
    /**
     * Generate the specific feedback. This is feedback that varies according to
     * the response the student gave. This method is only called if the display options
     * allow this to be shown.
     *
     * @param question_attempt $qa the question attempt to display.
     * @return string HTML fragment.
     */
    protected function specific_feedback(question_attempt $qa) {
        return parent::specific_feedback($qa);
    }

    /**
     * Generates an automatic description of the correct response to this question.
     * Not all question types can do this. If it is not possible, this method
     * should just return an empty string.
     *
     * @param question_attempt $qa the question attempt to display.
     * @return string HTML fragment.
     */
    protected function correct_response(question_attempt $qa) {
        return parent::correct_response($qa);
    }


}


/**
 * A base class to abstract out the differences between different type of
 * response format.
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
     * @param int $lines approximate size of input box to display.
     * @param object $context the context teh output belongs to.
     * @return string html to display the response.
     */
    public abstract function response_area_read_only($name, question_attempt $qa,
            question_attempt_step $step, $context);

    /**
     * Render the students respone when the question is in read-only mode.
     * @param string $name the variable name this input edits.
     * @param question_attempt $qa the question attempt being display.
     * @param question_attempt_step $step the current step.
     * @param int $lines approximate size of input box to display.
     * @param object $context the context teh output belongs to.
     * @return string html to display the response for editing.
     */
    public abstract function response_area_input($name, question_attempt $qa,
            question_attempt_step $step, $context);

    /**
     * @return string specific class name to add to the input element.
     */
    protected abstract function class_name();
}

class qtype_code_monaco_renderer extends qtype_code_format_renderer_base {
    
    /**
     * @return string the HTML for the textarea.
     */
    protected function textarea($response, $lines, $attributes) {
        $attributes['class'] = $this->class_name() . ' qtype_code_response form-control';
        $attributes['rows'] = $lines;
        $attributes['cols'] = 60;
        return html_writer::tag('textarea', s($response), $attributes);
    }

    protected function class_name() {
        return 'qtype_code_plain';
    }

    public function response_area_read_only($name, $qa, $step, $context) {
        $id = $qa->get_qt_field_name($name) . '_id';
        $this->page->requires->js(new moodle_url('/question/type/code/view.js'));
        $url1 = new moodle_url('/question/type/code/monaco-editor/min/vs/loader.js');
        $url2 = new moodle_url('/question/type/code/monaco-editor/min/vs');

        $responselabel = $this->displayoptions->add_question_identifier_to_label(get_string('answertext', 'qtype_code'));
        $output = html_writer::tag('label', $responselabel, ['class' => 'sr-only', 'for' => $id]);
        $output .= html_writer::tag('p', $qa->get_question()->language, array('id' => 'languageMonaco', 'style' => 'display:none'));
        $output .= html_writer::div(null, null, array('id' => 'containerMonaco', 'style' => 'width:800px;height:600px;border:1px solid grey'));
        $output .= html_writer::tag('p', $url1, array('id' => 'urlM1', 'style' => 'display:none'));
        $output .= html_writer::tag('p', $url2, array('id' => 'urlM2', 'style' => 'display:none'));
        $output .= html_writer::tag('p', s($step->get_qt_var($name)), array('id' => 'textMonaco', 'style' => 'display:none'));
        return $output;
    }

    public function response_area_input($name, $qa, $step, $context) {
        $this->page->requires->js(new moodle_url('/question/type/code/edit.js'));
        $url1 = new moodle_url('/question/type/code/monaco-editor/min/vs/loader.js');
        $url2 = new moodle_url('/question/type/code/monaco-editor/min/vs');

        

        $inputname = $qa->get_qt_field_name($name);
        $id = $inputname . '_id';

        $responselabel = $this->displayoptions->add_question_identifier_to_label(get_string('answertext', 'qtype_code'));
        $output = html_writer::tag('label', $responselabel, ['class' => 'sr-only', 'for' => $id]);
        $output .= $this->textarea($step->get_qt_var($name), 0, ['name' => $inputname, 'id' => $id, 'style' => 'display:none']);
        $output .= html_writer::tag('p', $qa->get_question()->language, array('id' => 'languageMonaco', 'style' => 'display:none'));
        $output .= html_writer::div(null, null, array('id' => 'containerMonaco', 'style' => 'width:615px;height:600px;border:1px solid grey'));
        $output .= html_writer::tag('p', $url1, array('id' => 'urlM1', 'style' => 'display:none'));
        $output .= html_writer::tag('p', $url2, array('id' => 'urlM2', 'style' => 'display:none'));
        $output .= html_writer::tag('p', $id, array('id' => 'monacoID', 'style' => 'display:none'));
        $output .= html_writer::tag('p', s($step->get_qt_var($name)), array('id' => 'textMonaco', 'style' => 'display:none'));
        $output .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => $inputname . 'format', 'value' => FORMAT_PLAIN]);

        return $output;
    }

}