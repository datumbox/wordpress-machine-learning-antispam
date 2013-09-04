<?php
/**
* Plugin Name: Machine Learning Antispam
* Plugin URI: http://www.datumbox.com
* Description: This Wordpress Plugin uses Machine Learning to detect spam and adult content comments and mark them as spam. Additionally it allows you to filter negative comments and keep them pending for approval.
* Version: 1.0
* Author: Vasilis Vryniotis
* Author URI: http://www.datumbox.com
* License: GPL2
*/

if (!function_exists('add_action')) {
    die(); //block direct web requests
}
require_once(dirname( __FILE__ ).'/DatumboxAPI.php'); //require the DatumboxAPI client to easily call Datumbox API

if (is_admin()) { //if admin include the admin specific functions
    require_once(dirname( __FILE__ ).'/options.php');
}

function machinelearningantispam_get_key() {
    return get_option('datumbox_api_key'); //return the api key of datumbox
}

function machinelearningantispam_call_datumbox($commentText,$type_of_check) {
    $apiKey=machinelearningantispam_get_key(); //fetch the API key
    if($apiKey==false || $apiKey=='') {
        return true; //don't block the comment if the plugin is not well configured
    }
    
    $DatumboxAPI = new DatumboxAPI($apiKey); //initialize DatumboxAPI Client
    
    if($type_of_check=='spam') {
        $response=$DatumboxAPI->SpamDetection($commentText); //Call Spam Detection service
        
        if($response=='spam') { //if spam return false
            return false;
        }
    }
    else if($type_of_check=='adult') {
        $response=$DatumboxAPI->AdultContentDetection($commentText); //Call Adult Content Detection service
        
        if($response=='adult') { //if adult return false
            return false;
        }
    }
    else if($type_of_check=='negative') {
        $response=$DatumboxAPI->SentimentAnalysis($commentText); //Call Sentiment Analysis service
        
        if($response=='negative') { //if negative return false
            return false;
        }
    }
    
    unset($DatumboxAPI);
    
    return true;
}

function machinelearningantispam_check_comment($commentdata) {
    
    if(get_option('machinelearningantispam_filterspam') && machinelearningantispam_call_datumbox($commentdata['comment_content'],'spam')==false) {
        //if Spam filtering is on and the Datumbox Service considers it spam then mark it as spam
        add_filter('pre_comment_approved', 'machinelearningantispam_result_spam');
    }
    else if(get_option('machinelearningantispam_filteradult') && machinelearningantispam_call_datumbox($commentdata['comment_content'],'adult')==false) {
        //if Adult filtering is on and the Datumbox Service considers it adult then mark it as spam
        add_filter('pre_comment_approved', 'machinelearningantispam_result_spam');
    }
    else if(get_option('machinelearningantispam_filternegative') && machinelearningantispam_call_datumbox($commentdata['comment_content'],'negative')==false) {
        //if Negative filtering is on and the Datumbox Service considers it negative then mark it as pending
        add_filter('pre_comment_approved', 'machinelearningantispam_result_pending');
    }
    
    return $commentdata;
}

function machinelearningantispam_result_spam() {
    return 'spam';
}

function machinelearningantispam_result_pending() {
    return 0;
}

add_action( 'preprocess_comment' , 'machinelearningantispam_check_comment' ); 