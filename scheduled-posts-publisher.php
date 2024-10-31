<?php
/**
 * Plugin Name: Scheduled Posts Publisher
 * Description: This plugin publish scheduled posts that were missed by the server's cron
 * Version: 1.0
 * Author: Bayu Prahasto
 * Author URI: http://obaytek.com
 * License: GPL2
 */

if(!class_exists('ObayMissedPosts')) {
	class ObayMissedPosts  {
		function __construct(){
			add_action('pre_get_posts', array($this, 'publishMissedPosts')); 
		}

		function publishMissedPosts() {
			global $wpdb, $pagenow;

			if (is_front_page() || is_page() || is_single() || is_singular() || is_404() || ( $pagenow == 'edit.php' )) {
				$now = gmdate('Y-m-d H:i:s');
			
				$args = array(
					'public'                => true,
					'exclude_from_search'   => false,
					'_builtin'              => false
				); 
				$post_types = get_post_types($args,'names','and');
				$post_types['post'] = 'post';
				$post_types['page'] = 'page';

				$str = implode ('\',\'',$post_types);
				
				$sql = "SELECT ID FROM $wpdb->posts WHERE post_type IN ('$str') AND post_status = 'future' AND post_date_gmt < '$now' ORDER BY post_date_gmt ASC LIMIT 1000";
				$hasil = $wpdb->get_results($sql);
				if($hasil) {
					foreach($hasil as $h) {
						wp_publish_post($h->ID);
					}
				}
			}
		}
	}
}

new ObayMissedPosts();