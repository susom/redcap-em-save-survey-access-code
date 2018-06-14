<?php
namespace Stanford\SaveSurveyAccessCode;

use \Plugin as Plugin;

class SaveSurveyAccessCode extends \ExternalModules\AbstractExternalModule {


    private $field;


    function hook_save_record($project_id, $record = NULL, $instrument, $event_id, $group_id = NULL, $survey_hash = NULL, $response_id = NULL, $repeat_instance = 1)
    {

        $this->saveAccessCode($project_id, $record,$instrument, $event_id);
    }


    /**
     *
     * @param $record
     * @param $instrument
     * @param $event_id
     */
    function saveAccessCode($project_id, $record, $instrument, $event_id) {

        Plugin::log($project_id, "DEBUG", "SAVING SURVEY ACCESS CODE FORPROJECT ID");

        Plugin::log($record ." | " . $instrument  ." | " . $event_id , "DEBUG", "INCOMING");
        $triggering_form = $this->getProjectSetting('triggering_form');
        $event_triggering_form = $this->getProjectSetting('event_triggering_form');
        $access_code_field = $this->getProjectSetting('access_code_field');
        $event_access_code_field = $this->getProjectSetting('event_access_code_field');
        $survey_form = $this->getProjectSetting('survey_form');
        $event_survey_form = $this->getProjectSetting('event_survey_form');
        $url_field = $this->getProjectSetting('url_field');

        //if classic, just set to true
        //else return whether the event-id is the same as the one passed in.
        $event_trigger = (\REDCap::isLongitudinal() ? $event_id == $event_triggering_form : true);
        //Plugin::log($triggering_form ." | " . $event_trigger, "DEBUG", "from config:");

        if (($instrument == $triggering_form) && ($event_trigger)) {

            $event_id = (\REDCap::isLongitudinal() ? $event_access_code_field : null);

            //Check to see if already loaded
            $get_data = array(
                \REDCap::getRecordIdField(),
                $url_field,
                $access_code_field
            );
            $q = \REDCap::getData('json',$record,$get_data, $event_id);
            $records = json_decode($q,true);
            $save_data = current($records);

            //if either of them are unset then carry on
            if (isset($url_field) && empty($save_data[$url_field]) ||
                isset($access_code_field) && empty($save_data[$access_code_field]) ) {

                //first need to create the survey link
                $event_survey = (\REDCap::isLongitudinal() ? $event_survey_form : null);
                $url = \REDCap::getSurveyLink($record, $survey_form, $event_survey);

                //if url_field is set, then add to save_data
                if (isset($url_field)) {
                    $save_data[$url_field] = $url;
                }

                //separate out the hash
                $re = '/.*\/?s=(.*)/m';
                preg_match_all($re, $url, $matches, PREG_SET_ORDER, 0);
                $hash = $matches[0][1];

                //if access_code isset get access code
                $access_code = $this->getSurveyAccessCode($hash);

                if (!empty($access_code)) {
                    $save_data[$access_code_field] = $access_code;
                }

                if (\REDCap::isLongitudinal()) {
                    $event_url_field_name = \REDCap::getEventNames(true, false, $event_access_code_field);
                    $save_data['redcap_event_name'] = $event_url_field_name;
                }

                //Plugin::log($save_data, "DEBUG", "SAVE DATA");
                \REDCap::saveData('json', json_encode(array($save_data)));

            }
        }
    }


    function getSurveyAccessCode($hash) {
        //we need to trigger generation of the access code

        // Get participant_id from the hash
        $sql = sprintf(
            "select participant_id from redcap_surveys_participants 
                    where hash = '%s';",
                    $hash);
        $q = db_query($sql);

        if ($error = db_error()) {
            Plugin::log($error, "ERROR", "ERROR RUNNING SQL ");
            return null;
        }

        $participant_id = db_result($q,0);

        $access_code = \Survey::getAccessCode($participant_id, 0);

        return $access_code;

    }



}

