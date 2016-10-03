<?php
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

        // Get self-completion status
        //$completion = $info->get_completion($USER->id, COMPLETION_CRITERIA_TYPE_SELF);

        // Check this user is enroled
        if (!$info->is_tracked_user($USER->id)) {
            $this->content->text = 'You are not enrolled';
            return $this->content;
        }

        // Is course complete?
        if ($info->is_course_complete($USER->id)) {
            $this->content->text = '';
            $this->content->footer = '<br><a href="../blocks/resetcompletion/reset_user_completion.php?course=' . $this->page->course->id . '">';
            $this->content->footer .= 'Reset Completion' . '</a>';
            return $this->content;
        } 
        // Check if the user has already marked themselves as complete
        /*
        else if ($completion->is_complete()) {
            $this->content->text = '';
            $this->content->footer = '<br><a href="../blocks/resetcompletion/reset_user_completion.php?course=' . $this->page->course->id . '">';
            $this->content->footer .= 'Reset Completion </a>';
            return $this->content;
        // If user is not complete, or has not yet self completed
         
        } */

        else {
            $this->content->text = 'You have not completed the course yet';
            return $this->content;
        }

        return $this->content;
    }
}


