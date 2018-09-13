<?php

/** @var \Stanford\SaveSurveyAccessCode\SaveSurveyAccessCode $module */
$module->emDebug("------- Starting  Save Survey Access Code -------");

$event_name = $_POST['redcap_event_name'];
$event_id = (!empty($event_name) ? \REDCap::getEventIdFromUniqueEvent($event_name) : null);

$module->emDebug("EVENT ID $event_id /PID :  $project_id / INSTRUMENT : ".$_POST['instrument']." / RECID : ".$_POST['record']);

$module->saveAccessCode($project_id,$_POST['record'],$_POST['instrument'],$event_id);

