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

        // Check this user is enroled
        if (!$info->is_tracked_user($USER->id)) {
            $this->content->text = 'You are not enrolled';
            return $this->content;
        }

        // Is course complete?
        if ($info->is_course_complete($USER->id)) {
            $this->content->text = 'Click the link below to reset your completion data for this course. <br/><br/>WARNING: It will delete your completion data for this course. Only use this if you require recertification, or if you wish to completely retake the course from scratch.';
            $this->content->footer = '<br><a href="../blocks/resetcompletion/reset_user_completion.php?course=' . $this->page->course->id . '">';
            $this->content->footer .= 'Reset Completion' . '</a>';
            return $this->content;
        } 

        else {
            $this->content->text = 'You have not completed the course yet';
            return $this->content;
        }

        return $this->content;
    }
}


