<?php
/**
 * @package music-player
 * @version 1.0
 */
/*
Plugin Name: music-player
Plugin URI: http://wordpress.org/plugins/music-player/
Description: Publish and manage your music on your blog. 
Author: jiali
Version: 1.0
Author URI: http://localhost/
setting : http://localhost/wordpress/wp-admin/options-general.php?page=music_settings
*/


class Music{
    // 构造函数
	function __construct(){
		add_action( 'init', array( $this, 'custom_post_type' ) );
		add_action( 'init',array($this,'custom_taxonomy' ) );
		add_action( 'add_meta_boxes',array($this,'music_meta_boxes' ) );
		add_action( 'save_post',array($this,'save_music_meta_data' ) );
		add_filter( 'manage_music_posts_columns', array($this,'add_music_column') );
		add_action( 'manage_music_posts_custom_column',array($this,'music_columns_content' ), 10, 2 );
		
		add_action( 'admin_menu',array($this,'add_music_setting' ) );
		//add_filter('the_content', array( $this, 'add_music_page' ) );
		add_action( 'admin_footer', array($this,'media_selector_print_scripts') );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ),array($this,'music_play_add_settings_link'));

		add_shortcode( 'show_all_music', array( $this, 'show_all_music_shortcode' ) );
		add_shortcode( 'get_music_by_id', array( $this, 'get_music_by_id' ) );
		add_action( 'init', array( $this, 'music_player_textdomain' ) ,1);
		add_action('restrict_manage_posts',array( $this,'music_category_drop_list') );
		
	}

	
    /**
	 * 添加后台音乐设置菜单
	 */
    function add_music_setting(){
    	//页名称，菜单名称，访问级别，菜单别名，点击该菜单时的回调函数（用以显示设置页面）
    	add_submenu_page('options-general.php',
    		'音乐设置','音乐设置','manage_options',
    		'music_settings',
    		array( $this,'music_setting_html')) ;
    }
    /**
	 * 音乐设置的显示页面
	 */
    function music_setting_html(){
    	$settings = get_option('music_settings');
    	//print_r($settings);
      ?>
      <h1><?php _e('Enhanced media library settings','music-player');?></h1>
      <form method="post" action="options.php" >
	      <?php wp_nonce_field('update-options') ?>
	      <div>
	      <label><?php _e('Play','music-player');?></label>
		      <input type="checkbox" name="music_settings[auto_play]" <?php checked(isset($settings['auto_play'])) ?>><?php _e('Auto Play','music-player');?>
		      <input type="checkbox" name="music_settings[circle_play]" <?php checked(isset($settings['circle_play'])) ?> ><?php _e('Circle Play','music-player');?>
	      </div>
	      <div>
		      <label><?php _e('Other','music-player');?></label>
		      <input type="checkbox" name="music_settings[clear_data]"  <?php checked(isset($settings['clear_data'])); ?>><?php _e('Clear data when uninstall Plugin','music-player');?>
	      </div>

	      <p class="submit">
				<input type="hidden" name="action" value="update">
				<input type="hidden" name="page_options" value="music_settings">
				<input type="submit" value="<?php _e('Save','music-player');?>" class="button-primary save-btn">
		  </p>
      </form>
      <?php
    }


	/**
	 * 添加音乐类型
	 */
	function custom_post_type(){
		$args =  array(
	       'labels'      => [
	           'name'                   => __('Music','music-player'),
	           'singular_name'          => __('Music','music-player'),
	           'add_new'                => __('Add Music','music-player'),
	           'add_new_item'           => __('Add Music','music-player'),
	           'edit_item'              => __('Edit Music','music-player'),
	           'view_item'              => __('View Music','music-player'),
	           'search_items'           => __('Search Music','music-player'),
				'featured_image'        => __('Music Cover','music-player'),
				'set_featured_image'    => __('Set Music Cover','music-player'),
				'remove_featured_image' => __('Remove Music Cover','music-player'),
				'use_featured_image'    => __('Set As Music Cover','music-player'),
	       ],

	       'public'      => true,
	       'has_archive' => true,
	       'menu_icon' => 'dashicons-format-audio',
	       'menu_position' => 5,

	       'supports' => array(
	       		'title',
	       		'editor',
	       		'thumbnail',
	       		'comments'
	       	)
	    );
		register_post_type( 'music', $args );
    }

    /**
	 * 添加音乐分类 
	 */

    function custom_taxonomy(){

    	 $taxonomy = array(
		'slug'         => 'music_category',
		'post_type'    => 'music',
		'hierarchical' => false,
	     );

    	 $labels = array(
    	 	'name'               =>__( 'Music Category','music-player' ),
    	 	'singular_name'      =>__( 'Music Category','music-player' ),
    	 	'search_items'       =>__( 'Search Music Category','music-player'),
    	 	'all_items'          =>__( 'All Music Category','music-player'),
    	 	'parent_item'        =>__( 'Parent Music','music-player' ),
    	 	'parent_item_colon'  => __( 'Parent Music', 'music-player' ),
			'edit_item'          => __( 'Edit Music Category', 'music-player' ),
			'update_item'        => __( 'Update Music Category', 'music-player' ),
			'add_new_item'       => __( 'Add New Music Category', 'music-player' ),
			'new_item_name'      => __( 'New Music Category Name', 'music-player' ),
			'menu_name'          => __( 'Music Category', 'music-player' ),

    	 	);
 
    	 $hierarchical = isset( $taxonomy['hierarchical'] ) ? $taxonomy['hierarchical'] : true;
    	 $rewrite = isset( $taxonomy['rewrite'] ) ? $taxonomy['rewrite'] : array( 'slug' => $taxonomy['slug'] );

    	 $args = array(
    	 	'labels' => $labels,
	    	'hierarchical'      => $hierarchical,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => $rewrite,

    	 	);
    	 register_taxonomy( 'music_category','music',$args );
    }


    /**
	 * 添加音乐管理页面的分类
	 */
    function music_category_drop_list(){
    	// global $typenow;
    	// //global $wp_query;
    	// print_r($typenow);
    	// //print_r($wp_query);
    	// $data = $GLOBALS['wp_the_query'];

    	// print_r($GLOBALS['wp_the_query']);
    	// $taxonomy = "music-category";
    	// $a = $data->query[$taxonomy];
    	// print_r($a);

    	    global $typenow;
		    global $wp_query;
		    //print_r($wp_query);
		    if ( $typenow == 'music' ) {
				$taxonomy = 'music_category';
				$taxname = 'Music Category';

				$selected = isset($wp_query->query[$taxonomy]) ? $wp_query->query[$taxonomy] : '';
				//print_r($selected);
				$data = get_terms( $taxonomy );

				print_r($data);

				wp_dropdown_categories(array(
					'show_option_all' =>  __('All Music Categories','music-player'),
					'taxonomy'        =>  $taxonomy,
					'name'            =>  $taxname,
					'value_field'     =>  'slug',
					'orderby'         =>  'name',
					'selected'        =>  $selected,
					'show_count'      =>  true,
					'hide_empty'      =>  true,
				));
		    }

    }
    

    /**
	 * 添加音乐的元数据
	 */
    function music_meta_boxes(){
    	add_meta_box(
    		'music_media_library',
    		__( 'Media Library', 'music-player' ),
    		array( $this,'music_desc_meta_box_html' ),
    		'music'
    		);
    }
    /**
	 * 音乐元数据的内容显示
	 */
    function music_desc_meta_box_html(){	
        global $post;
    	// $vo = get_post_meta($post->ID,'music_desc_nonce',true);
    	// if($vo){
    	//  $music_url = $vo['music_url'];
    	//  $singer = $vo['singer'];
    	//  $music_special = $vo['music_special'];
    	//  $bit_rate = $vo['bit_rate'];
    	//  $bit_rate_mode = $vo['bit_rate_mode'];
    	// }

       	 $new_music_url = wp_get_attachment_url( get_option( 'media_selector_attachment_id' ) );
    	 $music_url = $new_music_url ? $new_music_url: get_post_meta($post->ID,'music_url',true);
    	 $singer = get_post_meta($post->ID,'singer',true);
    	 $music_special = get_post_meta($post->ID,'music_special',true);
    	 $bit_rate = get_post_meta($post->ID,'bit_rate',true);
    	 $bit_rate_mode = get_post_meta($post->ID,'bit_rate_mode',true);
    ?>  
		<form method="post" action="options.php" >
		<?php wp_nonce_field( 'save_music_desc','music_desc_nonce' );?>
		<table class="form-table">
		  <tr>
			<th style="width:20%"><label for="music_url" ><?php _e('Music URL','music-player');?></label></th>

			<td>
				<input id="music_url" name="music_url" type="text"  class="regular-text" 
			    value="<?php if(isset($music_url )){echo esc_attr( $music_url );}?>">
				<input id="upload_music_button" type="button" class="button" value="<?php _e('Upload Music','music-player'); ?>" >
				<input type='hidden' name='music_attachment_id' id='music_attachment_id' value=''>
		    </td>
		  </tr>

		  <tr>
			<th style="width:20%"><label for="singer" style="width:20%"><?php _e('Singer','music-player');?></label></th>
			<td><input name="singer" type="text"  class="regular-text " value="<?php if(isset( $singer )){echo esc_attr( $singer ); }?>">
			</td>
		  </tr>

		  <tr>
			<th style="width:20%"><label for="music_special"><?php _e('Album','music-player');?></label></th>
			<td>
				<input name="music_special" type="text"  class="regular-text " value="<?php if(isset( $music_special )){ echo esc_attr( $music_special); }?>">
			</td>
		  </tr>

		  <tr>
			<th style="width:20%"><label for="bit_rate"><?php _e('Bate Rate','music-player');?></label></th>
			<td>
				<input name="bit_rate" type="text"  class="regular-text" value="<?php if(isset( $bit_rate )){ echo esc_attr( $bit_rate ); }?>">
			</td>
		  </tr>

		  <tr>
			<th style="width:20%"><label for="bit_rate_mode"><?php _e('Bate Rate Mode','music-player');?></label></th>
			<td>
				<input name="bit_rate_mode" type="text"  class="regular-text " value="<?php if(isset( $bit_rate_mode )){ echo esc_attr( $bit_rate_mode );} ?>">
			</td>
		  </tr>
		</table>
			
	</form>

	<?php
    }
    /**
	 * 保存音乐元数据
	 */
     function save_music_meta_data( $post_id ){
     		//验证
		if( ! isset($_POST['music_desc_nonce']) || ! wp_verify_nonce( $_POST['music_desc_nonce'], 'save_music_desc' ) ){
			return $post_id;
		}

		//自动保存检查
		if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
			return $post_id;
		}

		if( 'music' != $_POST['post_type'] ){
			return $post_id;
		}

		//检查权限
		if( 'page' == $_POST['post_type'] ){
			if ( !current_user_can('edit_page', $post_id) ){
				return $post_id;
			}
		}
		elseif( ! current_user_can('edit_post', $post_id) ){
			return $post_id;
		}

		// $music_desc['music_url'] = $_POST['music_url'];
		// $music_desc['singer'] = $_POST['singer'];
		// $music_desc['music_special'] = $_POST['music_special'];
		// $music_desc['bit_rate'] = $_POST['bit_rate'];
		// $music_desc['bit_rate_mode'] = $_POST['bit_rate_mode'];

		// update_post_meta( $post_id, 'music_desc_nonce', $music_desc );

		update_post_meta( $post_id, 'music_url',  $_POST['music_url'] );
		update_post_meta( $post_id, 'singer',  $_POST['singer'] );
		update_post_meta( $post_id, 'music_special',  $_POST['music_special'] );
		update_post_meta( $post_id, 'bit_rate',  $_POST['bit_rate'] );
		update_post_meta( $post_id, 'bit_rate_mode',  $_POST['bit_rate_mode'] );

     }
     /**
	 * 添加音乐的媒体库
	 */
     function media_selector_print_scripts(){
     	global $current_screen;

     	if( $current_screen->post_type != 'music' ) return;
     	$my_saved_attachment_post_id = get_option( 'media_selector_attachment_id', 0 );
     	?>
		<script type='text/javascript'>
		jQuery( document ).ready( function( $ ) {
			// Uploading files
			var file_frame;
			var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
			var set_to_post_id = <?php echo $my_saved_attachment_post_id; ?>; // Set this
			jQuery('#upload_music_button').on('click', function( event ){
				event.preventDefault();
				// If the media frame already exists, reopen it.
				if ( file_frame ) {
					// Set the post ID to what we want
					file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
					// Open frame
					file_frame.open();
					return;
				} else {
					// Set the wp.media post id so the uploader grabs the ID we want when initialised
					wp.media.model.settings.post.id = set_to_post_id;
				}
				// Create the media frame.
				file_frame = wp.media.frames.file_frame = wp.media({
					title: 'Select a song to upload',
					button: {
						text: 'Use this song',
					},
					multiple: false	// Set to true to allow multiple files to be selected
				});
				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
					// We set multiple to false so only get one image from the uploader
					attachment = file_frame.state().get('selection').first().toJSON();
					// Do something with attachment.id and/or attachment.url here
				    $( '#music_url' ).val( attachment.url );
					// Restore the main post ID
					wp.media.model.settings.post.id = wp_media_post_id;
				});
					// Finally, open the modal
					file_frame.open();
			});
			// Restore the main ID when the add media button is pressed
			jQuery( 'a.add_media' ).on( 'click', function() {
				wp.media.model.settings.post.id = wp_media_post_id;
			});
		});
	</script><?php	
     }

     /**
	 * 音乐列表新增播放column
	 */
     function add_music_column($columns){
     	return array_merge($columns,
     		array(
                 
                  'singer' =>  __('singer','music-player'),
                  'music_special' => __('music_special','music-player'),
                  'bit_rate' => __('bit_rate','music-player'),
                  'bit_rate_mode' => __('bit_rate_mode','music-player'),
                  'music_play' => __('music_play','music-player')
     			) );
     	
     }
     /**
	 * 音乐列表
	 */
     function music_columns_content($column, $post_id){
     	//$column = get_post_meta_id($post_id);
     	//print_r($desc);

     	//print_r($column);
     	switch($column){
     		case 'singer':
     			echo get_post_meta( $post_id,'singer',true);
			break;

     		case 'music_special':
     			echo get_post_meta( $post_id,'music_special',true);
     		break;

     		case "bit_rate":
     			echo get_post_meta( $post_id,'bit_rate',true);
     		break;

     		case "bit_rate_mode":
     			 echo get_post_meta( $post_id,'bit_rate_mode',true);
     		break;

     		case "music_play":
     		// echo get_post_meta( $post_id,'music_url',true);
     			?>
				
     			 <video src="<?php echo get_post_meta( get_the_ID(),'music_url',true); ?>" controls height="50px">
				<!--   你的浏览器不支持 <code>video</code> 标签. -->
				 </video>

     			<?php
     		break;

     	}
     }

     
     /**
	 * 添加音乐插件列表的设置连接
	 */
     function music_play_add_settings_link( $links ){
     	return array_merge(
     		 array(
            'settings' => '<a href="options-general.php?page=music_settings">'.__('settings').'</a>'
             ),
              $links
     		);
     }
    /**
	 * 短码--显示所有音乐
	 */
     function show_all_music_shortcode(){

     	 $music_posts = get_posts(array(
     		'numberposts' => 10,
     		'post_type' => 'music',
 
     		));
     	 if( $music_posts ){
     	 	foreach ($music_posts as $key => $value) {
     	 		//print_r($value);
	     	  $thumbnail = wp_get_attachment_image_src( get_post_meta($value->ID,'_thumbnail_id',true) );
	     	  //print_r($thumbnail[0]);post_title
     ?>
     	 <img src="<?php  echo $thumbnail[0]; ?>" width="100px" height="100px">
     	 <a href="<?php  echo $value->guid; ?>"> <?php  echo $value->post_title; ?></a>
	    <video src="<?php  echo get_post_meta( $value->ID,'music_url',true); ?>" controls >
	       你的浏览器不支持 <code>video</code> 标签.
 	     </video>
    <?php
    		}
     	 }
     	// print_r($music_posts);
     }

    

     function get_music_by_id( $atts ){
     	   $title = get_post( $atts['post_id'] )->post_title;
     	   $content = get_post( $atts['post_id'] )->post_content;
     	   // print_r( $title);
     	   // print_r($content);
     	   $data = get_post_meta( $atts['post_id'] );
     	   // print_r($data);
     	   // echo the_title();
     	   // echo the_content();
     	   $image_url = wp_get_attachment_image_src($data['_thumbnail_id'][0]);
     	   // print_r($image_url);
     	   // print_r($data['music_url'][0]);
     	   // print_r($data['singer'][0]);
     	   // print_r($data['music_special'][0]);
     	   // print_r($data['bit_rate'][0]);
     	   // print_r($data['bit_rate_mode'][0]);
     	  ?>
     	  	<img src="<?php echo isset($image_url[0]) ? $image_url[0] :'' ;?>">
     	  	<span><?php echo isset($title) ? $title : '' ;?></span>

     	  	<video src="<?php  echo isset($data['music_url'][0]) ? $data['music_url'][0] :''; ?>" controls>
	       你的浏览器不支持 <code>video</code> 标签.
 	        </video>
 	        <p> <?php echo isset( $content ) ? $content : '';?></p>
 	        <p>
 	          <span> 歌手:<?php echo isset( $data['singer'][0] ) ? $data['singer'][0] : '';?></span> <br>
 	          <span> 专辑:<?php echo isset( $data['music_special'][0] ) ? $data['music_special'][0] : '';?></span> <br>
 	          <span> 比特率:<?php echo isset( $data['bit_rate'][0] ) ? $data['bit_rate'][0] : ''; echo isset( $data['bit_rate_mode'][0] ) ? $data['bit_rate_mode'][0] : '';?></span> 
 	        </p>

     	  <?php
     	// return $data;
     	
     }

 	/**
 	 * 汉化
 	 */
     function music_player_textdomain(){
		load_plugin_textdomain( 'music-player', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	}


	new Music();

?>
