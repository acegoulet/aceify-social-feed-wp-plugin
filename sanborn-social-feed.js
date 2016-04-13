
$(document).ready(function(){
    social_endpoint = $('#social-feed-list').data('endpoint');
    use_twitter = $('#social-feed-list').data('twitter');
    use_insta = $('#social-feed-list').data('instagram');
    post_count = $('#social-feed-list').data('count');
    
    social_feed = [];
    twitter_feed_array = [];
    instagram_feed_array = [];
    if(use_twitter == 1){
    	twitter_feed(social_endpoint, use_insta, post_count, social_feed, twitter_feed_array, instagram_feed_array);
	}
	else if(use_insta == 1){
    	instagram_feed(social_endpoint, use_insta, post_count, social_feed, twitter_feed_array, instagram_feed_array);
	}
	
	function twitter_feed(social_endpoint, use_insta, post_count, social_feed, twitter_feed_array, instagram_feed_array){
    	$.ajax({
    		url: social_endpoint+'/twitter.php',
    		type: 'GET',
    		dataType: 'json',
    		success: function(data) {
        		for (var i = 0; i < data.length; i++) {
            		var tw_date = Date.parse(data[i].created_at);
            		var tw_media = 'no_image';
            		if(typeof data[i].entities.media != 'undefined' && typeof data[i].entities.media[0] != 'undefined' && typeof data[i].entities.media[0].media_url_https != 'undefined'){
                		tw_media = data[i].entities.media[0].media_url_https;
            		}
                    var tweet_array_item = new Array('tweet', tw_date, data[i].text, 'https://twitter.com/'+data[i].user.screen_name+'/status/'+data[i].id_str, tw_media, 'twitter-post', data[i].user.screen_name);
                    social_feed.push(tweet_array_item);
                    twitter_feed_array.push(tweet_array_item);
    			}
    		},
    		complete: function(){
        		if(use_insta == 1){
                	instagram_feed(social_endpoint, use_insta, post_count, social_feed, twitter_feed_array, instagram_feed_array);
            	}
            	else {
                	print_feed( social_feed, post_count, twitter_feed_array, instagram_feed_array, 0 );
            	}
    		}
    	});
	}
	
	function instagram_feed(social_endpoint, use_insta, post_count, social_feed, twitter_feed_array, instagram_feed_array) {
    	$.ajax({
    		url: social_endpoint+'/instagram.php',
    		type: 'GET',
    		dataType: 'json',
    		success: function(data) {
        		for (var i = 0; i < data.data.length; i++) {
            		var insta_date = data.data[i].created_time*1000;
                    var insta_caption = '';
                    if(data.data[i].caption){
    	        		insta_caption = data.data[i].caption.text;
            		}
            		var instagram_array_item = new Array('instagram', insta_date, insta_caption, data.data[i].link, data.data[i].images.standard_resolution.url, data.data[i].type, data.data[i].user.username);
                    social_feed.push(instagram_array_item);
                    instagram_feed_array.push(instagram_array_item);
    			}
    		},
    		complete: function(){
        		print_feed( social_feed, post_count, twitter_feed_array, instagram_feed_array, 0 );
    		}
    	});
	}
	
	output_count = 0;
	function print_feed( social_feed, post_count, twitter_feed_array, instagram_feed_array, offset) {
    	function compare(a,b) {
    		if (a[1] > b[1])
    			return -1;
    		if (a[1] < b[1])
    			return 1;
    		return 0;
    	}
    	social_feed.sort(compare);
    	
    	var feed_length = social_feed.length;
    	var loop_end = post_count;
    	
    	if(offset > 0){
        	var loop_end = offset + post_count;
    	}
    	    	
    	if($('#social-feed-list').length) {
        	for (var i = offset; i < loop_end; i++) {
            	if(typeof social_feed[i] != 'undefined'){
                	var social_type = 'text';
                	if(social_feed[i][4] !== 'no_image'){
                    	social_type = 'image';
                	}
                	var social_item_output = '<a class="social-item '+ social_type + ' ' + social_feed[i][0] +'" href="'+ social_feed[i][3] +'" target="_blank">';
                	    if(social_feed[i][4] !== 'no_image'){
                    	    social_item_output += '<img class="social-image" src="'+ social_feed[i][4] +'" alt="" />';
                	    }
                	    social_item_output += '<div class="social-caption">'+ social_feed[i][2] +'</div>';
                	    social_item_output += '<div class="social-meta">';
                	        social_item_output += '<span class="social-icon ' + social_feed[i][0] +'"></span>';
                	        if(social_feed[i][0] == 'tweet'){
                    	        social_item_output += 'Tweeted on ';
                	        } else if(social_feed[i][0] == 'instagram'){
                    	        social_item_output += 'Grammed on ';
                	        }
                	        var social_item_date = new Date(parseInt(social_feed[i][1]));
                	        social_item_output += (social_item_date.getMonth()+1)+"/"+social_item_date.getDate()+"/"+social_item_date.getFullYear();
                	    social_item_output += '</div>';
                	social_item_output += '</a>';
                    $('#social-feed-list').append(social_item_output);
                    output_count++;
                }
            }
            $('.social-item').animate({opacity: 1}, 600);
            $('#more-social-wrapper .loading').fadeOut();
    	
        	if(feed_length > post_count){
            	$('#load-more-social-button').show();
        	} else {
            	$('#load-more-social-button').hide();
        	}
        	if(output_count >= feed_length){
            	$('#load-more-social-button').hide();
        	}
    	}
	}
	
	//show more
	var offset_base = post_count;
	var offset_load = 0;
	$('#load-more-social-button').click(function(){
    	//$('#more-social-wrapper .loading').fadeIn();
    	offset_load = offset_load + offset_base;
    	print_feed( social_feed, post_count, twitter_feed_array, instagram_feed_array, offset_load);
	});
});