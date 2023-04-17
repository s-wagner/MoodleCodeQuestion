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
 * Question definition class for code.
 *
 * @package     qtype_code
 * @copyright   2023 Stefan Wagner
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Class that represents a code question.
 */
class qtype_code_question extends question_with_responses {


    /**
     * @var array contains response template
     */
    public $responsetemplate;

    /**
     * @var string programming language to be used for question
     */
    public $language;

    /**
     * @var integer 0 or 1 enbales intellisense
     */
    public int $intel;

    /**
     * @var integer 0 or 1 enables inline intellisense
     */
    public int $inline;

    /**
     * @var integer 0 or 1 enables auto complete for keywords
     */
    public int $keywords;

    /**
     * @var integer 0 or 1 enables auto complete for language specific variables
     */
    public int $variables;

    /**
     * @var integer 0 or 1 enables auto complete for functions
     */
    public int $functions;

    /**
     * @var integer 0 or 1 enables auto complete for classes
     */
    public int $classes;

    /**
     * @var integer 0 or 1 enables auto complete for modules
     */
    public int $modules;

    /**
     * @var integer size of tabs in monaco editor
     */
    public int $tabsize;

    /**
     * Returns the data that would need to be submitted to get a correct answer.
     *
     * @return array|null Null if it is not possible to compute a correct response.
     */
    public function get_correct_response() {
        return null;
    }

    /**
     * Sets the behaviour of the question
     *
     * @param question_attempt $qa current question_attempt
     * @param string $preferredbehaviour prefred behaviour
     * @return question_behaviour
     */
    public function make_behaviour(question_attempt $qa, $preferredbehaviour) {
        return question_engine::make_behaviour('manualgraded', $qa, $preferredbehaviour);
    }


    /**
     * Returns the expected data fields
     *
     * @return array
     */
    public function get_expected_data() {
        $expecteddata = array('answer' => PARAM_RAW);
        return $expecteddata;
    }

    /**
     * Checks if the response is complete
     *
     * @param array $response current response
     * @return boolean
     */
    public function is_complete_response(array $response) {
        return array_key_exists('answer', $response) && ($response['answer'] !== '');
    }

    /**
     * Checks if two responses are the same
     *
     * @param array $prevresponse previous response
     * @param array $newresponse new response
     * @return boolean
     */
    public function is_same_response(array $prevresponse, array $newresponse) {
        if (array_key_exists('answer', $prevresponse) && $prevresponse['answer'] !== $this->responsetemplate) {
            $value1 = (string) $prevresponse['answer'];
        } else {
            $value1 = '';
        }
        if (array_key_exists('answer', $newresponse) && $newresponse['answer'] !== $this->responsetemplate) {
            $value2 = (string) $newresponse['answer'];
        } else {
            $value2 = '';
        }
        return $value1 === $value2;
    }

    /**
     * Summarises the response
     *
     * @param array $response current response
     * @return string
     */
    public function summarise_response(array $response) {
        $output = null;

        if (isset($response['answer'])) {
            $output .= $response['answer'];
        }

        return $output;
    }

    /**
     * Unsummarises the response
     *
     * @param string $summary summary of response
     * @return array
     */
    public function un_summarise_response(string $summary) {
        if (empty($summary)) {
            return [];
        }

        return ['answer' => $summary, 'answerformat' => FORMAT_PLAIN];
    }
}
