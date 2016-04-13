<?php
header('Content-type: application/json');

//load WP headers to get variables
require( '../../../wp-blog-header.php' );
$insta_handle = get_option('sanborn_social_feed_widget_instagram_handle');
$insta_client_id = get_option('sanborn_social_feed_widget_insta_client_id');
$insta_client_secret = get_option('sanborn_social_feed_widget_insta_client_secret');

if(!empty($insta_handle) && !empty($insta_client_id) && !empty($insta_client_secret)){
    $user_id_endpoint = "https://api.instagram.com/v1/users/search?q=".strtolower($insta_handle)."&client_id=".stripslashes($insta_client_id);
    $user_id = '';
    $get_user_id = wp_remote_get($user_id_endpoint);
    if(empty($get_user_id->errors)){
        $user_id_json = json_decode($get_user_id['body']);
                
        foreach($user_id_json->data as $user){
            if($user->username == strtolower($insta_handle)){
                $user_id = $user->id;
                break;
            }
        }
    } 
    
    if(!empty($user_id)){
        $json_link = 'https://api.instagram.com/v1/users/'.$user_id.'/media/recent/?client_id='.stripslashes($insta_client_id);
        $get_insta_feed = wp_remote_get($json_link);
        $json = $get_insta_feed['body'];
    }
    
}
else {
    $json = '';
}

echo $json;
?>