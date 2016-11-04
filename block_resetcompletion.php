<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 
/**
 * Displays the reset block
 * @package    block_resetcompletion
 * @copyright  2016 Andrew Park
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once($CFG->libdir.'/completionlib.php');
class block_resetcompletion extends block_base {
    public function init() {
        $this->title = get_string('resetcompletion', 'block_resetcompletion');
    }
    public function get_content() { 
        global $CFG, $USER;

        // If content is cached
        if ($this->content !== NULL) {
          return $this->content;
        }

        // Create empty content
        $this->content = new stdClass;

        // Get course completion data
        $info = new completion_info($this->page->course);

        // Check this user is enroled
        if (!$info->is_tracked_user($USER->id)) {
            $this->content->text = get_string('unenrolled', 'block_resetcompletion');
            return $this->content;
        }

        // Is course complete?
        if ($info->is_course_complete($USER->id)) {
            $this->content->text = get_string('resetcontenttext', 'block_resetcompletion');
            $this->content->footer = 
                '<br/><a href="../blocks/resetcompletion/reset_user_completion.php?course=' . 
                $this->page->course->id . 
                '&sesskey=' . sesskey();
            $this->content->footer .= '">' . get_string('pluginname', 'block_resetcompletion')  . '</a>';
            return $this->content;
        } 

        else {
            $this->content->text = get_string('resetincompletetext', 'block_resetcompletion');
            return $this->content;
        }

        return $this->content;
    }
}


