<?php
    require_once('../../config.php');
    require_once($CFG->libdir.'/completionlib.php');
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');
    defined('MOODLE_INTERNAL') || die(); 
    
    $PAGE->set_context(context_system::instance());
    $courseid = optional_param('course', 0, PARAM_INT);
    $user = $USER->id;
    
    global $DB;
    
    // Delete individual module completion records for current user
    $DB->delete_records_select('course_modules_completion',
            'coursemoduleid IN (SELECT id FROM mdl_course_modules WHERE course=?) AND userid=?',
            array($courseid, $user));
    // Delete course completion records
    $DB->delete_records('course_completions', array('course' => $courseid, 'userid' => $user));
    $DB->delete_records('course_completion_crit_compl', array('course' => $courseid, 'userid' => $user));
    // Delete any user choices
    $DB->delete_records_select('choice_answers',
            'choiceid IN (SELECT id FROM mdl_choice WHERE course=?) AND userid=?',
            array($courseid, $user));
    // Delete SCORM data related to the user
    $DB->delete_records_select('scorm_scoes_track',
            'scormid IN (SELECT id FROM mdl_scorm WHERE course=?) AND userid=?',
            array($courseid, $user));

    // Delete orphaned quiz attempts made by the user, after the completion record for the quiz has been deleted
    $orphanedattempts = $DB->get_records_sql_menu("
        SELECT id, uniqueid
          FROM {quiz_attempts}
        WHERE userid=$user AND quiz IN (SELECT id FROM mdl_quiz WHERE course=$courseid)");
          
    if ($orphanedattempts) {
       foreach ($orphanedattempts as $attemptid => $usageid) {
           question_engine::delete_questions_usage_by_activity($usageid);
           $DB->delete_records('quiz_attempts', array('id' => $attemptid));
       }
    }
    
    cache::make('core', 'completion')->purge();
    redirect($CFG->wwwroot . '/course/view.php?id=' . $courseid);

?>
