<?php

use \Plugin as Plugin;
/** @var \Stanford\SaveSurveyAccessCode\SaveSurveyAccessCode $module */

Plugin::log("------- GET URL for Save Survey Access Code -------");
//Plugin::log($project_id, "DEBUG","PID");


//noauth= false, api = true
$url = $module->getUrl('TriggerMigration.php',false, true);

Plugin::log($url, "DEBUG", "DET URL");

echo "This is your URL for the DET: <br>".$url;
