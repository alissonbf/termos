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
 * Arquivo que mostra as politicas de uso dos cursos
 *
 * @copyright 2012 Alisson Barbosa Ferreira <http://onload.com.br>.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package mod
 * @subpackage termos
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/resourcelib.php');

$agree      = optional_param('agree', 0, PARAM_BOOL);
$course_id  = optional_param('course_id',0,PARAM_INT);
$termos_id  = optional_param('termos_id',0,PARAM_INT);

$PAGE->set_url('/mod/termos/policy.php');
$PAGE->set_popup_notification_allowed(false);

if (!isloggedin()) {
    require_login();
}

if (isguestuser()) {
    $sitepolicy = $CFG->sitepolicyguest;
} else {
    $sitepolicy = $CFG->sitepolicy;
}

if (!empty($SESSION->wantsurl)) {
    $return = $SESSION->wantsurl;
} else {
    $return = $CFG->wwwroot.'/';
}

if ($agree and confirm_sesskey()) {    // User has agreed
    if (!isguestuser()) {              // Don't remember guests        
        $record = new stdClass();
        
        $record->termos_id  = $termos_id;
        $record->user_id    = $USER->id;
        $record->agreed     = 1;
        
        $usuario = $DB->get_record('termos_agreed', array('user_id'=>$USER->id,'termos_id'=>$termos_id));
        
        if(!$usuario){
            $DB->insert_record('termos_agreed', $record, false);
        }
    } 
    redirect($CFG->wwwroot.'/course/view.php?id='.$course_id);
}

$strpolicyagree = get_string('policyagree','termos');
$strpolicyagreement = get_string('policyagreement','termos');

$PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
$PAGE->set_title($strpolicyagreement);
$PAGE->set_heading($SITE->fullname);
$PAGE->navbar->add($strpolicyagreement);

echo $OUTPUT->header();
echo $OUTPUT->heading($strpolicyagreement);

$termos  = $DB->get_record('termos', array('course'=>$course_id));

if ($termos) {
    echo $OUTPUT->box($termos->intro);
}

echo "<br />";
echo "<br />";

$formcontinue   = new single_button(new moodle_url('policy.php', array('agree'=>1,'termos_id'=>$termos->id,'course_id'=>$course_id)), get_string('yes'));
$formcancel     = new single_button(new moodle_url($CFG->wwwroot, array('agree'=>0,'termos_id'=>0)), get_string('no'));
echo $OUTPUT->confirm($strpolicyagree, $formcontinue, $formcancel);

echo $OUTPUT->footer();
