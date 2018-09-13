<?php

/** @var \Stanford\SaveSurveyAccessCode\SaveSurveyAccessCode $module */

//$module->emLog("------- GET URL for Save Survey Access Code in $project_id -------");

$url = $module->getUrl('TriggerMigration.php', false, true);
echo "This is your URL for the DET: <br>".$url;