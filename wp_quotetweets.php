<?php
/*
Plugin Name: WP Quote Tweets
Version: 4.1.2
Description: Allows authors to quote Twitter tweets in posts or pages using a simple shortcode. [qtweet 123456789].
Plugin URI:  http://0xtc.com/2009/06/18/wordpress-plugin-wp-quote-tweets.xhtml
Contributors: 0xtc
Author: Tanin Ehrami
Author URI: http://0xtc.com/
Stable tag: trunk
*/

/* 
															ini_set("display_errors","2");
															ERROR_REPORTING(E_ALL);
*/
if (!class_exists("wpQuoteTweet")) {
	class wpQuoteTweet {
		var $OptionsNamePre = "wpQuoteTweetAdminOptions";
		var $prefs = NULL;
		function wpQuoteTweet() {
			$this->prefs = $this->getAdminOptions();
			add_shortcode ('quotetweet', array(&$this, 'wp_quote_tweet_exec'));
			add_shortcode ('qtweet', array(&$this, 'wp_quote_tweet_exec'));
		}

		function init() {
			$this->prefs = $this->getAdminOptions();
		}

		function getAdminOptions() {
			$wpQuoteTwAdminOptions = array('template' => 'twitter','CustomTemplate'=>null,'wpQuoteTw3rdSRV'=>'awesome');
			$qtwOptions = get_option($this->OptionsNamePre);
			if (!empty($qtwOptions)) {
				foreach ($qtwOptions as $key => $option)
					$wpQuoteTwAdminOptions[$key] = $option;
			}				
			update_option($this->OptionsNamePre, $wpQuoteTwAdminOptions);
			return $wpQuoteTwAdminOptions;
		}

		function wp_quote_tweet_add_header() {
			$qtwOptions = $this->prefs;
			if ($qtwOptions['template'] == $qtwOptions['CustomTemplate']){
				$templatefilepath = get_bloginfo('template_directory').'/wpqt/'. $qtwOptions['CustomTemplate'].'/'.  $qtwOptions['CustomTemplate'].'.css';
			} else {			
				$templatefilepath = get_bloginfo('wpurl').'/wp-content/plugins/wp-quote-tweets/templates/'.$qtwOptions['template'].'/'.$qtwOptions['template'].'.css';
			}
			$cssline = "\r\n\t\t<!-- WP Quote Tweets 4.1.0 -->\r\n\t\t<link rel=\"stylesheet\" href=\"".$templatefilepath."\" type=\"text/css\" media=\"screen\" />\n";			
			echo $cssline;
		}

		function printAdminPage() {
			$qtwOptions = $this->prefs;
			if (isset($_POST['update_wpQuoteTweetSettings'])) {
				if (isset($_POST['wpQuoteTwTemplate'])) {
					if (strrpos($_POST['wpQuoteTwTemplate'],'wpqt_custom_') !== false){
						echo 'Setting custom template';
						$qtwOptions['CustomTemplate'] = substr($_POST['wpQuoteTwTemplate'],12);
						$qtwOptions['template'] = substr($_POST['wpQuoteTwTemplate'],12);
					} else {
						$qtwOptions['template'] = $_POST['wpQuoteTwTemplate'];
						$qtwOptions['CustomTemplate'] = null;
					}
				}
				if ('OMG_OMG_AWESOME'== $_POST['wpQuoteTweetUser3rdPartyImageSRV']){
					$qtwOptions['wpQuoteTw3rdSRV']= 'awesome';
				} else {
					$qtwOptions['wpQuoteTw3rdSRV']= 'not_so_awesome';
				}
				update_option($this->OptionsNamePre, $qtwOptions);
				?>
					<div class="updated"><p><strong><?php _e("Settings Updated", "wpQuoteTweet");?></strong></p></div>
				<?php
			}
			if (isset($_POST['wpQuoteTweetClearCache'])) {
				if ($_POST['wpQuoteTweetClearCache']=='please') {
						$cachedir = dirname(__FILE__).'/xmlcache/'; 
						if (file_exists(WP_CONTENT_DIR.'/cache/xmlcache/.')){
							$cachedir = WP_CONTENT_DIR.'/cache/xmlcache/'; 
						}
						$d = dir($cachedir); 
						while($entry = $d->read()) { 
							if ($entry!= "." && $entry != "..") { 
								unlink($cachedir.$entry);
							}
						}
						$d->close();
				}
				?>
					<div class="updated"><p><strong><?php _e("Cache cleared", "wpQuoteTweet");?></strong></p></div>
				<?php
			}
			echo '
			<div class="wrap">
				<div id="icon-options-general" class="icon32"><br /></div> 
				<h2>WP Quote Tweets</h2>
				<div id="poststuff" class="metabox-holder has-right-sidebar">
					<div id="post-body"> 
						<div id="post-body-content">';
							echo '
							<div id="templateselect" class="stuffbox">
								<h3 class="hndle">Settings</h3>
								<div class="inside">
									<form method="post" action="'. $_SERVER["REQUEST_URI"].'">
										<h2>Images</h2>
										<div>
											<p>
											<label for="wpQuoteTweetUser3rdPartyImageSRV">';
									if ('awesome' == $qtwOptions['wpQuoteTw3rdSRV']){
										$selected=' checked="checked"';
									} else {
										$selected='';
									}
											echo '
												<input type="checkbox" '.$selected.' name="wpQuoteTweetUser3rdPartyImageSRV" value="OMG_OMG_AWESOME" id="wpQuoteTweetUser3rdPartyImageSRV" /> Use <a href="http://tweetimag.es/" target="_blank">tweetimag.es</a> for avatar images (Reduces the risk of missing pictures).
											</label>
											</p>
										</div>
										<h2>Select a template</h2>';
									$dirPath = dirname(__FILE__).'/templates/';
									if ($handle = opendir($dirPath)) {
										while (false !== ($file = readdir($handle))) {
											if ($file != "." && $file != "..") {
												if (file_exists("$dirPath/$file/$file.template.html")) {
													if (($file == $qtwOptions['template'])&& !$qtwOptions['CustomTemplate']){
														$selected=' checked="checked"';
													} else {
														$selected='';
													}
													echo '
										<div style="border-bottom:1px solid #ddd;border-top:1px solid #fff;">
											<h4>'.$file.'</h4>
											<input type="radio"'.$selected.' value="'.$file.'" name="wpQuoteTwTemplate" /> 
											<img style="vertical-align:middle" src="'.WP_PLUGIN_URL.'/wp-quote-tweets/templates/'.$file.'/'.$file.'.png" />
											<br />
											<p style="width:600px">';
												if (file_exists("$dirPath/$file/readme.txt")){
													echo file_get_contents("$dirPath/$file/readme.txt");
												}
												echo '<br />
											</p>
										</div>	
										';
												}
											}
										}
										closedir($handle);
									}
									/*	look for custom templates in the template folder	*/									
									$dirPath = (TEMPLATEPATH).'/wpqt/';
									if (file_exists($dirPath)) if ($handle = opendir($dirPath)) {
										while (false !== ($file = readdir($handle))) {
											if ($file != "." && $file != "..") {
												if (file_exists("$dirPath/$file/$file.template.html")) {
													if (($file == $qtwOptions['template']) && ($qtwOptions['CustomTemplate'] != '')){
														$selected=' checked="checked"';
													} else {
														$selected='';
													}
													echo '
													<div style="border-bottom:1px solid #ddd;border-top:1px solid #fff;">
														<h4>'.$file.'</h4>
														<input type="radio"'.$selected.' value="wpqt_custom_'.$file.'" name="wpQuoteTwTemplate" />';
														if (file_exists((TEMPLATEPATH).'/wpqt/'.$file.'/'. $file. '.png')){
															echo '<img style="vertical-align:middle" src="'.get_bloginfo('template_directory').'/wpqt/'.$file.'/'.$file.'.png" />
															<br />';
														}
														echo '
														<p style="width:600px">';
															if (file_exists("$dirPath/$file/readme.txt")){
																echo file_get_contents("$dirPath/$file/readme.txt");
															}
															echo '<br />
														</p>
													</div>	
													';
												}
											}
										}
										closedir($handle);
									}
										echo '
										<div>
											<p>
											<label for="wpQuoteTweetClearCache">
												<input type="checkbox" name="wpQuoteTweetClearCache" value="please" id="wpQuoteTweetClearCache" /> Clear the XML cache when I save.
											</label>
											</p>
										</div>
										<div class="submit" style="float:right;">
												<input type="submit" name="update_wpQuoteTweetSettings" value="';
												_e('Update Settings', 'wpQuoteTweet');
												echo '" />
										</div>
									</form>
									<br class="clear" />
								</div>
							</div>
						</div>
					</div>

					<div id="side-info-column" class="inner-sidebar"> 
						<div id="side-sortables" class="meta-box-sortables"> 
							<div id="wpqthelpdiv" class="postbox " > 
								<h3 class="hndle"><span>Just so you know....</span></h3> 
								<div class="inside"> 
									<h4>Templates</h4>
									<p>
										If you\'ve created your own custom template that matches your site and style, you can save its directory in a <code>wpqt</code> subdirectory in your current theme\'s path. 
									</p>
									<p>
										For example: <code>/wp-content/themes/MyTheme/<strong>wpqt</strong>/MyCustomTweetTemplate/</code>
									</p>
									<p>
										To learn more about custom templates, read the <a href="http://0xtc.com/2009/07/09/creating-a-wp-quote-tweets-template.xhtml" title="Make your own templates!" target="_blank">templating documentation</a> on 0xtc.com.
									</p>
									<h4>Caching</h4>
									<p>
										This plugin caches the information it gets from Twitter.com so your server plays nice and doesn\'t make too many requests.
									</p>
									<p>
										Should something ever go wrong and you need to clear the cache, check the checkbox at the bottom of this page and save your settings to delete all the cached requests.
									</p>
								</div>
							</div>
						</div>
					</div>					
				</div>
			</div>';
		}

		// will create content using cached xml data
		function wp_quote_tweet_exec($att,$content=null){
			$qtwOptions = $this->prefs;
			extract	(shortcode_atts(array('tweetid'=>false), $att));
			if ($tweetid==false){
				if (is_numeric($att[0])){
					$tweetid=$att[0];
				} else {
					return false;
				}
			}

			$templatefilepath = dirname(__FILE__).'/templates/'.$this->prefs['template'].'/'.$this->prefs['template'].'.template.html';

			if ( $this->prefs['template'] == $this->prefs['CustomTemplate']){
				$templatefilepath = (TEMPLATEPATH).'/wpqt/'.$this->prefs['template'].'/'.$this->prefs['template'].'.template.html';
			}
			
			if (is_feed()){
				$templatefilepath = dirname(__FILE__).'/templates/feed/feed.template.html';
			}

			if (file_exists($templatefilepath)) {
				$QuoteTemplate = file_get_contents($templatefilepath);
			} else {
				return '<strong>Could not find the following template file : '.$templatefilepath.'</strong>';
			}

			$status = $this->wp_quote_tweet_getTwitterStatus($tweetid, true);

			if (($status==false) || $status->user->id==''){return '<code>Failed loading tweet '.$tweetid.'</code>';}
			
			$r_content = '';
			if ($status->in_reply_to_user_id <> ''){
				$userinfo = $this->wp_quote_tweet_getTwitterUser($status->in_reply_to_user_id);
				if ($status->in_reply_to_status_id <> ''){
					$replyToStr = ' in reply to <a href="http://twitter.com/'.$userinfo->screen_name.'/status/'.$status->in_reply_to_status_id.'" rel="external">'.$userinfo->name.'</a>';
				} else {
					$replyToStr = ' in reply to '.$userinfo->name;
				}
			} else {
				$replyToStr = '';
			}
			if ($status->user->profile_background_tile == 'true'){
				$tweetbgCSSprop = 'repeat-x';
			} else {
				$tweetbgCSSprop = 'no-repeat';
			}
			$findArr = Array(
				'%TWEET_URL%',
				'%REPLY_TO_LINK%',
				'%RETWEET_LINK%',
				'%USER_PICTURE_LINK%',
				'%PROFILE_LINK%',
				'%TIMESTAMP_LINK%',
				'%NICE_TIMESTAMP_LINK%',
				'%IN_REPLY_TO_LINK%',
				'%CREATED_AT%',
				'%NICE_CREATED_AT%',
				'%TWEET_ID%',
				'%TWEET_TEXT%',
				'%FROM_LINK%',
				'%IN_REPLY_TO_STATUS_ID%',
				'%IN_REPLY_TO_USER_ID%',
				'%IN_REPLY_TO_SCREEN_NAME%',
				'%USER_ID%',
				'%USER_NAME%',
				'%USER_SCREENNAME%',
				'%USER_LOCATION%',
				'%USER_DESCRIPTION%',
				'%USER_PROFILE_IMAGE_URL%',
				'%USER_URL%',
				'%USER_FOLLOWERS_COUNT%',
				'%USER_PROFILE_BACKGROUND_COLOR%',
				'%USER_PROFILE_TEXT_COLOR%',
				'%USER_PROFILE_LINK_COLOR%',
				'%USER_PROFILE_SIDEBAR_FILL_COLOR%',
				'%USER_PROFILE_SIDEBAR_BORDER_COLOR%',
				'%USER_FRIEND_COUNT%',
				'%USER_CREATED_AT%',
				'%USER_FAVOURITES_COUNT%',
				'%USER_UTC_OFFSET%',
				'%USER_TIME_ZONE%',
				'%USER_PROFILE_BACKGROUND_IMAGE_URL%',
				'%USER_STATUSES_COUNT%',
				'%CSS_USER_BACKGROUND%'
			);
			$clean_and_calculated_time = date("j M Y H:i:s", strtotime($status->created_at));
			if ($qtwOptions['wpQuoteTw3rdSRV']=='awesome') {
				$status->user->profile_image_url = 'http://img.tweetimag.es/i/'.$status->user->screen_name.'_n';
			}
			$replaceArr = Array(
				'http://twitter.com/'.$status->user->screen_name.'/status/'.$status->id,
				'<a href="http://twitter.com/home?status=@'.$status->user->screen_name.'%20&amp;in_reply_to_status_id='.$status->id.'&amp;in_reply_to='.$status->user->screen_name.'" title="Reply to '.$status->user->name.'" rel="external" class="reply">Reply</a>',
				'<a href="http://twitter.com/home?status=RT @'.$status->user->screen_name.'%20'.urlencode(strip_tags($this->wp_quote_tweet_formatTweetContent($status->text))).'" title="Reply to '.$status->user->name.'" rel="external" class="retweet">Retweet</a>',
				'<a href="http://twitter.com/'.$status->user->screen_name.'" title="'.$status->user->name.'" class="quoting_pic" rel="external"><img src="'.$status->user->profile_image_url.'" alt="'.$status->user->screen_name.'" /></a>',
				'<a href="http://twitter.com/'.$status->user->screen_name.'" title="Twitter page : '.$status->user->name.'" rel="external">'. $status->user->screen_name.'</a>',
				'<a href="http://twitter.com/'.$status->user->screen_name.'/status/'.$status->id.'" rel="external">'.date("j-n-Y H:i:s",strtotime($clean_and_calculated_time)).'</a>',
				'<a href="http://twitter.com/'.$status->user->screen_name.'/status/'.$status->id.'" rel="external">'.$this->wp_quote_tweet_niceTime(strtotime($clean_and_calculated_time)).'</a>',
				$replyToStr,
				$status->created_at,
				$this->wp_quote_tweet_niceTime(strtotime($clean_and_calculated_time)),
				$status->id,
				$this->wp_quote_tweet_formatTweetContent($status->text)."\r\n",
				$status->source,
				$status->in_reply_to_status_id,
				$status->in_reply_to_user_id,
				$status->in_reply_to_screen_name,
				$status->user->id,
				$status->user->name,
				$status->user->screen_name,
				$status->user->location,
				$status->user->description,
				$status->user->profile_image_url,
				$status->user->url,
				$status->user->followers_count,
				$status->user->profile_background_color,
				$status->user->profile_text_color,
				$status->user->profile_link_color,
				$status->user->profile_sidebar_fill_color,
				$status->user->profile_sidebar_border_color,
				$status->user->friends_count,
				$status->user->created_at,
				$status->user->favourites_count,
				$status->user->utc_offset,
				$status->user->time_zone,
				$status->user->profile_background_image_url,
				$status->user->statuses_count,
				'background:#'.$status->user->profile_background_color.' url('.$status->user->profile_background_image_url.') top left '.$tweetbgCSSprop
			);
			$r_content = str_ireplace($findArr, $replaceArr, $QuoteTemplate);
			return $r_content;
		}

		function wp_quote_tweet_formatTweetContent ($tweet){
			$search = array('&'); 
			$replace = array('&amp;');
			$tweet = str_replace($search, $replace, $tweet);
			$tweet = preg_replace("/http:\/\/(.*?)[^ ]*/"	, 	'<a href="\\0" rel="external">\\0</a>', $tweet);
			$tweet = preg_replace("(@([a-zA-Z0-9_]+))"		,	"<a href=\"http://www.twitter.com/\\1\" rel=\"external\">\\0</a>", $tweet);
			$tweet = preg_replace('/\#(\w+)/'				, 	"<a href='http://search.twitter.com/search?q=$1' rel='external'>#$1</a>", $tweet);
			return $tweet;
		}

		// will cache and return xml
		function wp_quote_tweet_getTwitterStatus($tweetID,$rs=false) {
			$url = 'http://twitter.com/statuses/show/'.$tweetID.'.xml?ustring='.uniqid('s',true);
			$TwitterCacheDir = dirname(__FILE__).'/xmlcache/';
			if (file_exists(WP_CONTENT_DIR.'/cache/xmlcache/.')){
				$TwitterCacheDir = WP_CONTENT_DIR.'/cache/xmlcache/'; 
			}
			$filename = $TwitterCacheDir . 'twitterTweet.'.$tweetID.'.xml';
			$metafilename = $TwitterCacheDir . 'twitterTweet.'.$tweetID.'.xml.meta';
			if (file_exists($filename)) {
				$content = file_get_contents($filename);
				if (stristr($content,'<!DOCTYPE HTML PUBLIC')){
					// "I'm afraid I can't do that Dave...."
					unlink($filename);
					// something went horribly wrong on twitter's end
					return false;
					// try again later...
				}
			} else {
				$curl_handle=curl_init();
				curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
				curl_setopt($curl_handle,CURLOPT_URL,$url);
				curl_setopt($curl_handle,CURLOPT_HEADER,$header);
				$content = curl_exec($curl_handle);
				$httpCode = curl_getinfo($curl_handle);
				$meta .= print_r($httpCode,true)."\r\n";
				curl_close($curl_handle);
				
				// this is only for debugging
				if (is_writable($TwitterCacheDir)){
//					file_put_contents($metafilename, $meta);
				}
				if ($httpCode['http_code']==404) {return false;}
				if ($httpCode['http_code']==500) {return false;}
				if ($httpCode['http_code']==503) {return false;}
				
				if (stristr($content,'<!DOCTYPE HTML PUBLIC')){return false;}
				if (stristr($content,'<HTML>')){return false;}
				if (stristr($content,'<hash>')){return false;}
				
				if (is_writable($TwitterCacheDir)){
					file_put_contents($filename, $content);
				}
			}
			
			try {
				$status = new SimpleXMLElement($content);
			} catch (exception $e) {
				return false;
			}
			if (isset($status->error)){
				unlink($filename);
				return false;
			}
			if ($rs == true){return $status;}
			$tweets = array();
			$message='';
			$message			= $this->wp_quote_tweet_formatTweetContent($status->text);
			$time 				= $this->wp_quote_tweet_niceTime(strtotime(str_replace("+0000", "", $status->created_at)));
			$time 				= date("j-n-Y H:i:s",strtotime(str_replace(" +0000", "", $status->created_at )));
			$profile_image_url	= $status->user->profile_image_url;
			$StatusID			= $status->id;
			$TweetUserName		= $status->user->screen_name;
			$TweeterName		= $status->user->name;
			$tweets[] = array(
				'in_reply_to_status_id'	=>	$status->in_reply_to_status_id,
				'in_reply_to_user_id'	=>	$status->in_reply_to_user_id,
				'message' 				=> 	$message,
				'time' 					=> 	$time,
				'profile_image_url'		=>	$profile_image_url,
				'StatusID'				=>	$StatusID,
				'UserName'				=>	$TweetUserName,
				'Name'					=>	$TweeterName);
			if ($TweetUserName==''){
				return false;
			}
			return $tweets;
		}

		function wp_quote_tweet_niceTime($time) {
			$delta = time() - $time;
			if ($delta < 60) { return 'less than a minute ago.';
				} else if ($delta < 120) { return 'about a minute ago.';
				} else if ($delta < (45 * 60)) { return floor($delta / 60) . ' minutes ago.';
				} else if ($delta < (90 * 60)) { return 'about an hour ago.';
				} else if ($delta < (24 * 60 * 60)) { return 'about ' . floor($delta / 3600) . ' hours ago.';
				} else if ($delta < (48 * 60 * 60)) { return 'yesterday.';
				} else if ($delta < (168 * 60 * 60)) { return floor($delta / 86400).' days ago.';
				} else { return date('g:i A M jS Y', $time);
			}
		}

		// will cache and return xml
		function wp_quote_tweet_getTwitterUser($userID){
			$url = 'http://twitter.com/users/show.xml?user_id='.$userID.'&ustring='.uniqid('s',true);;
			$TwitterCacheDir = dirname(__FILE__).'/xmlcache/';
			if (file_exists(WP_CONTENT_DIR.'/cache/xmlcache/.')){
				$TwitterCacheDir = WP_CONTENT_DIR.'/cache/xmlcache/'; 
			}
			$filename = $TwitterCacheDir . 'twitterUser.'.strtolower($userID).'.xml';
			$metafilename= $TwitterCacheDir . 'twitterUser.'.strtolower($userID).'.xml.meta';
			if (file_exists($filename)) {
				$content = file_get_contents($filename);				
				if (stristr($content,'<!DOCTYPE HTML PUBLIC')){
					unlink($filename);
					return false;
				}
				
			} else {
				$curl_handle=curl_init();
				curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
				curl_setopt($curl_handle,CURLOPT_URL,$url);
				curl_setopt($curl_handle,CURLOPT_HEADER,$header);
				$content = curl_exec($curl_handle);
				$httpCode = curl_getinfo($curl_handle);
				$meta .= print_r($httpCode,true)."\r\n";
				curl_close($curl_handle);

				// This is only for debugging.
				if (is_writable($TwitterCacheDir)){
//					file_put_contents($metafilename, $meta);
				}

				if ($httpCode['http_code']==404) {return false;}
				if ($httpCode['http_code']==500) {return false;}
				if ($httpCode['http_code']==503) {return false;}
				
				
				if (stristr($content,'<HTML>')){return false;}
				if (stristr($content,'<!DOCTYPE HTML PUBLIC')){return false;}
				if (stristr($content,'<hash>')){return false;}
				
				if (is_writable($TwitterCacheDir)){
					file_put_contents($filename, $content);
				}
			}
			try {
				$userinfo = new SimpleXMLElement($content);
			} catch (exception $e) {
				return false;
			}
			if (isset($userinfo->error)){
				unlink($filename);
				return false;
			}
			
			
			return $userinfo;
		}
	}
}

if (class_exists("wpQuoteTweet")) {
	$quoteTweets = new wpQuoteTweet();
}

if (!function_exists("wpQuoteTweet_ap")) {
	function wpQuoteTweet_ap() {
		global $quoteTweets;
		if (!isset($quoteTweets)) {
			return;
		}
		if (function_exists('add_options_page')) {
			add_options_page('WP Quote Tweets', 'WP Quote Tweets', 9, basename(__FILE__), array(&$quoteTweets, 'printAdminPage'));
		}
	}	
}

if (isset($quoteTweets)) {
	add_action('admin_menu', 'wpQuoteTweet_ap');
	add_action('wp_head', array(&$quoteTweets, 'wp_quote_tweet_add_header'), 1);
	add_action('activate_wp-quote-tweets/wp_quotetweets.php',  array(&$quoteTweets, 'init'));
}

?>