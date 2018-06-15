
<?php
define('NOAUTH',true);

use \Plugin as Plugin;
/** @var \Stanford\SaveSurveyAccessCode\SaveSurveyAccessCode $module */


Plugin::log("------- Starting  Save Survey Access Code -------");
Plugin::log($project_id, "DEBUG","PID");
Plugin::log($_REQUEST, "DEBUG", "det call");

$event_name = $_POST['redcap_event_name'];
$event_id = (!empty($event_name) ? \REDCap::getEventIdFromUniqueEvent($event_name) : null);

$module->saveAccessCode($project_id,$_POST['record'],$_POST['instrument'],$event_id);

