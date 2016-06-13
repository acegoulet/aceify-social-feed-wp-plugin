<?php
header('Content-type: application/json');

//load WP headers to get variables
require( '../../../wp-blog-header.php' );
$insta_access_token = get_option('sanborn_social_feed_widget_insta_access_token');

if(!empty($insta_access_token)){
    $json_link = 'https://api.instagram.com/v1/users/self/media/recent/?access_token='.stripslashes($insta_access_token);
    $get_insta_feed = wp_remote_get($json_link);
    $json = $get_insta_feed['body'];
}
    
else {
    $json = '';
}
$json_decode = json_decode($json);
if($json_decode->meta->code == 200){
    echo $json;
}
?>