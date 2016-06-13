<?php
/*
Plugin Name: Sanborn Social Feed
Plugin URI: http://www.sanbornmediafactory.com
Description: Plugin to create a social feed of twitter and instagram posts from user accounts
Author: Ace Goulet
Version: 1.0
Author URI: http://acegoulet.com
*/

// Define current version constant
define( 'sanborn_sf_version', '1.0' );

//Twitter widget - include this file in your theme functions or copy the code directly in there.

wp_register_sidebar_widget(
    'sanborn_social_feed_widget',          	// your unique widget id
    'Sanborn Social Feed',        // widget name
    'sanborn_social_feed_widget_display',  // callback function to display widget
    array(                                 // options
        'description' => 'Social feed generated from Twitter and Instagram posts.'
    )
);
wp_register_widget_control(
	'sanborn_social_feed_widget',		// id
	'sanborn_social_feed_widget',		// name
	'sanborn_social_feed_widget_control'	// callback function
);
function sanborn_social_feed_widget_control($args=array(), $params=array()) {
	//the form is submitted, save into database
	if (isset($_POST['submitted'])) {
		update_option('sanborn_social_feed_widget_postnum', $_POST['postnum']);
		update_option('sanborn_social_feed_widget_hide_load_more', $_POST['dont_include_load_more']);
		//twitter options
		update_option('sanborn_social_feed_widget_handle', $_POST['tw_handle']);
		update_option('sanborn_social_feed_widget_twitter_consumer_key', $_POST['tw_consumer_key']);
		update_option('sanborn_social_feed_widget_twitter_consumer_secret', $_POST['tw_consumer_secret']);
		update_option('sanborn_social_feed_widget_twitter_oauth_token', $_POST['tw_oauth_token']);
		update_option('sanborn_social_feed_widget_twitter_oauth_secret', $_POST['tw_oauth_secret']);
		//instagram options
		update_option('sanborn_social_feed_widget_insta_client_id', $_POST['insta_client_id']);
		update_option('sanborn_social_feed_widget_insta_access_token', $_POST['insta_access_token']);
		update_option('sanborn_social_feed_widget_insta_redirect_uri', $_POST['insta_redirect_uri']);
	}

	//load options
	$postnum = get_option('sanborn_social_feed_widget_postnum');
	$dont_include_load_more = get_option('sanborn_social_feed_widget_hide_load_more');
	//twitter options
	$tw_handle = get_option('sanborn_social_feed_widget_handle');
	$tw_consumer_key = get_option('sanborn_social_feed_widget_twitter_consumer_key');
	$tw_consumer_secret = get_option('sanborn_social_feed_widget_twitter_consumer_secret');
	$tw_oauth_token = get_option('sanborn_social_feed_widget_twitter_oauth_token');
	$tw_oauth_secret = get_option('sanborn_social_feed_widget_twitter_oauth_secret');
	//instagram options
	$insta_client_id = get_option('sanborn_social_feed_widget_insta_client_id');
	$insta_access_token = get_option('sanborn_social_feed_widget_insta_access_token');
	$insta_redirect_uri = get_option('sanborn_social_feed_widget_insta_redirect_uri');
	
	?>
	<h3>Twitter</h3>
	<label for="tw_handle">Twitter handle:</label><br />
	<input type="text" class="widefat" name="tw_handle" id="tw_handle" value="<?php echo stripslashes($tw_handle); ?>" />
	<br /><br />
	<label for="tw_consumer_key">Twitter App Consumer Key:</label><br />
	<input type="text" class="widefat" name="tw_consumer_key" id="tw_consumer_key" value="<?php echo stripslashes($tw_consumer_key); ?>" />
	<br /><br />
	<label for="tw_consumer_secret">Twitter App Consumer Secret:</label><br />
	<input type="text" class="widefat" name="tw_consumer_secret" id="tw_consumer_secret" value="<?php echo stripslashes($tw_consumer_secret); ?>" />
	<br /><br />
	<label for="tw_oauth_token">Twitter App OAuth Token:</label><br />
	<input type="text" class="widefat" name="tw_oauth_token" id="tw_oauth_token" value="<?php echo stripslashes($tw_oauth_token); ?>" />
	<br /><br />
	<label for="tw_oauth_secret">Twitter App OAuth Secret:</label><br />
	<input type="text" class="widefat" name="tw_oauth_secret" id="tw_oauth_secret" value="<?php echo stripslashes($tw_oauth_secret); ?>" />
	<br /><br />
	<hr />
	<h3>Instagram</h3>
	<label for="insta_client_id">Instagram App Client ID:</label><br />
	<input type="text" class="widefat" name="insta_client_id" id="insta_client_id" value="<?php echo stripslashes($insta_client_id); ?>" />
	<br /><br />
	<label for="insta_redirect_uri">Instagram App Redirect URI:</label><br />
	<input type="text" class="widefat" name="insta_redirect_uri" id="insta_redirect_uri" value="<?php echo stripslashes($insta_redirect_uri); ?>" />
	<br /><br />
	<label for="insta_access_token">Instagram Access Token:</label><br />
	<input type="text" class="widefat" name="insta_access_token" id="insta_access_token" value="<?php echo stripslashes($insta_access_token); ?>" />
	<?php if(!empty($insta_redirect_uri) && !empty($insta_client_id)){ ?>
	    <br /><br />Get it <a href="https://api.instagram.com/oauth/authorize/?client_id=<?php echo stripslashes($insta_client_id); ?>&redirect_uri=<?php echo stripslashes($insta_redirect_uri); ?>&response_type=token" target="_blank">here</a>. You will be authorizing the app to access posts associated with your Instagram account. After authorizing the app, you will see the access token in the browser url.
	<?php } else { ?>
    	<br /><br />Input Client ID and Redirect URI and save widget settings to get a link for generating the access token.
	<?php } ?>
	<br /><br />
	<hr />
	<br />
	<label for="postnum">Number of social posts to display:</label><br />
	<input type="number" min="1" class="widefat" name="postnum" id="postnum" value="<?php echo stripslashes($postnum); ?>" />
	<br /><br />
	<label for="dont_include_load_more">Hide load more button?</label><br />
	<input type="hidden" name="dont_include_load_more" value="show" />
	<input type="checkbox" value="true" name="dont_include_load_more" id="dont_include_load_more" <?php if(stripslashes($dont_include_load_more) == "true"){ echo 'checked'; } ?> />
	<br /><br />

	<input type="hidden" name="submitted" value="1" />
	<?php
}
function sanborn_social_feed_widget_display($args=array(), $params=array()) {
	//load options
	$postnum = get_option('sanborn_social_feed_widget_postnum');
	$dont_include_load_more = get_option('sanborn_social_feed_widget_hide_load_more');
	//twitter options
	$tw_handle = get_option('sanborn_social_feed_widget_handle');
	$tw_consumer_key = get_option('sanborn_social_feed_widget_twitter_consumer_key');
	$tw_consumer_secret = get_option('sanborn_social_feed_widget_twitter_consumer_secret');
	$tw_oauth_token = get_option('sanborn_social_feed_widget_twitter_oauth_token');
	$tw_oauth_secret = get_option('sanborn_social_feed_widget_twitter_oauth_secret');
	//instagram options
	$insta_client_id = get_option('sanborn_social_feed_widget_insta_client_id');
	$insta_access_token = get_option('sanborn_social_feed_widget_insta_access_token');
	$insta_redirect_uri = get_option('sanborn_social_feed_widget_insta_redirect_uri');

	//widget output
	wp_enqueue_script('sanborn-social-feed', plugins_url( 'sanborn-social-feed.js', __FILE__ ), 'jquery', '1', true);
	$use_twitter = false;
	$use_insta = false;
	if(!empty($tw_handle) && !empty($tw_consumer_key) && !empty($tw_consumer_secret) && !empty($tw_oauth_token) && !empty($tw_oauth_secret)){
    	$use_twitter = true;
	}
	if(!empty($insta_client_id) && !empty($insta_access_token) && !empty($insta_redirect_uri)){
    	$use_insta = true;
	}
	?>
	
	<div id="social-feed-wrapper">
        <div id="social-feed-list" data-count="<?php echo $postnum; ?>" data-endpoint="<?php echo plugins_url('', __FILE__ ) ?>" data-twitter="<?php echo $use_twitter; ?>" data-instagram="<?php echo $use_insta; ?>">
        </div>
        <div id="more-social-wrapper">
            <div class="loading"><i class="fa fa-spinner fa-spin"></i> Loading</div>
            <?php if($dont_include_load_more !== "true"){ ?>
                <a href="javascript:void(0);" class="button" id="load-more-social-button" style="display: none;">Load More Social</a>
            <?php } ?>
        </div>
	</div>
	
	<?php
	
}

?>