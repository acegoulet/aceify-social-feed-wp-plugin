<?php
//load WP headers to get variables
require( '../../../wp-blog-header.php' );
//set content type to json
header('Content-type: application/json');
//require the twitteroauth
require_once 'twitteroauth/twitteroauth.php';

//get variables from wordpress options
$tw_handle = get_option('aceify_social_feed_widget_handle');
$tw_consumer_key = get_option('aceify_social_feed_widget_twitter_consumer_key');
$tw_consumer_secret = get_option('aceify_social_feed_widget_twitter_consumer_secret');
$tw_oauth_token = get_option('aceify_social_feed_widget_twitter_oauth_token');
$tw_oauth_secret = get_option('aceify_social_feed_widget_twitter_oauth_secret');

if(!empty($tw_handle) && !empty($tw_consumer_key) && !empty($tw_consumer_secret) && !empty($tw_oauth_token) && !empty($tw_oauth_secret)){

    define('CONSUMER_KEY', stripslashes($tw_consumer_key)); 
    define('CONSUMER_SECRET', stripslashes($tw_consumer_secret)); 
    define('OAUTH_TOKEN', stripslashes($tw_oauth_token)); 
    define('OAUTH_SECRET', stripslashes($tw_oauth_secret)); 
    
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
    
    #METHODS FOR ACCESSING THE ENDPOINT ARE SENT VIA $_GET['method']
    $typeOfSearch = 'q';
    
    //get the type of method we're using to get info
    #not set = search tweets
    #'user' = user tweets
    $method = 'user';
    $include_rts = true;
    $query = stripslashes($tw_handle);
    //set up api paths based on the method
    switch ($method) {
    	case 'user':
    		$path = 'statuses/user_timeline';
    		$params = array('screen_name' => $query);
    		break;
    	default:
    		$path = 'search/tweets';
    		$params = array('q' => $query);
    		break;
    }
    
    //add any other paramaters that were sent over to
    //the parameters...these should correspond to twitter
    //params 1 to 1...so just send over params such as count=1
    //just as if you were sending them to twitter and they'll
    //get added on
    
    foreach($_GET as $key => $value){
    	if($key != 'method' && $key != 'search'){
    		$params[$key]=$value;
    	}
    }
    
    $content = $connection->get($path, $params);
    
    $json = json_encode($content);

}

else {
    $json = '';
}

echo $json;

?>