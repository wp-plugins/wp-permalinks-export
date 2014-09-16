<?php
/*
Plugin Name: WP Permalinks Export
Plugin URI: http://www.meow.fr
Description: Export all permalinks (posts, pages) along with titles.
Version: 0.2
Author: Jordy Meow
Author URI: http://www.meow.fr

Remarks: 
This plugin was inspired by a StackOverflow thread: 
http://stackoverflow.com/questions/3464701/export-list-of-pretty-permalinks-and-post-title

Dual licensed under the MIT and GPL licenses:
http://www.opensource.org/licenses/mit-license.php
http://www.gnu.org/licenses/gpl.html

Originally developed for two of my websites: 
- Totoro Times (http://www.totorotimes.com) 
- Haikyo (http://www.haikyo.org)
*/

if ( is_admin() ) {

	class Meow_Permalink_List {

		public function __construct() {
			add_filter( 'plugin_row_meta', array( $this, 'plugin_meta_links' ), 10, 2 );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}

		public function plugin_meta_links( $links, $file ) {
			if ( plugin_basename( __FILE__ ) !== $file )
				return $links;
			return array_merge( array(
					'download' => '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/tools.php?page=mwpe_permalinks-export&noheader=true">Export Permalinks</a>'
				), $links
			);
		}

		public function admin_menu() {
			add_submenu_page( null, "", "", "activate_plugins", "mwpe_permalinks-export", array( $this, "permalinks_list_export" ) );
		}

		public function permalinks_list_export() {
			ob_clean();
			header('Content-type:text/plain');
			$posts = new WP_Query('post_type=any&posts_per_page=-1&suppress_filters=1&post_status=publish');
			$posts = $posts->posts;
			echo "Type\tTitle\tPermalink\n";
			foreach($posts as $post) {
				switch ($post->post_type) {
					case 'revision':
					case 'nav_menu_item':
						break;
					case 'page':
						$permalink = get_page_link($post->ID);
						break;
					case 'post':
						$permalink = get_permalink($post->ID);
						break;
					case 'attachment':
						//$permalink = get_attachment_link($post->ID);
						break;
					default:
						//$permalink = get_post_permalink($post->ID);
						break;
				}
				echo "{$post->post_type}\t{$post->post_title}\t{$permalink}\n";
			}
			exit;
		}

	}

	$GLOBALS['Meow_Permalink_List'] = new Meow_Permalink_List;

}

?>