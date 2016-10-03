<?php
    require_once('../../config.php');
    require_once($CFG->libdir.'/completionlib.php');
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');
    defined('MOODLE_INTERNAL') || die(); 
    
    $PAGE->set_context(context_system::instance());
    $courseid = optional_param('course', 0, PARAM_INT);
    $user = $USER->id;
/*
    $conn = mysql_connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass)
        or die("Unable to connect to MySQL");
    
    $selected = mysql_select_db($CFG->dbname, $conn) 
        or die("Could not select Moodle db");
    
    if ($courseid && $user) {
        mysql_query('DELETE FROM mdl_course_modules_completion WHERE userid=' . $user . ' AND coursemoduleid IN (SELECT id FROM mdl_course_modules WHERE course=' . $courseid . ')');
        mysql_query('DELETE FROM mdl_course_completions WHERE userid=' . $user . ' AND course=' . $courseid);
        mysql_query('DELETE FROM mdl_course_completion_crit_compl WHERE userid=' . $user . ' AND course=' . $courseid);
        redirect($CFG->wwwroot . '/course/view.php?id=' . $courseid);
    } else {
        echo "Course reset failed"; 
        redirect($CFG->wwwroot . '/course/view.php?id=' . $courseid);
    }
 */
        global $DB;
        $DB->delete_records_select('course_modules_completion',
                'coursemoduleid IN (SELECT id FROM mdl_course_modules WHERE course=?) AND userid=?',
                array($courseid, $user));
        $DB->delete_records('course_completions', array('course' => $courseid, 'userid' => $user));
        $DB->delete_records('course_completion_crit_compl', array('course' => $courseid, 'userid' => $user));
        
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
