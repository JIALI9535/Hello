<?php
/**
 * @package Hello_Test
 * @version 1.0
 */
/*
Plugin Name: Hello Test
Plugin URI: http://wordpress.org/plugins/test-hello-world/
Description: This is  just a plugin
Author: jiali
Version: 1.0
Author URI: http://localhost/

*/

class Test_Plugin {

	function __construct(){

		add_filter('the_content', array( $this, 'add_copyright' ) );
		add_action( 'wp_footer', array( $this, 'hello_world' ) );
		add_action( 'wp_head', array( $this, 'hello_world_css' ) );
		add_action('admin_menu', array( $this, 'add_copyright_menu' ) );
		add_shortcode( 'add_num', array( $this, 'shortcode_test' ) );
		add_action('add_meta_boxes', array( $this, 'wporg_add_custom_box' ) );

		add_action( 'save_post', array( $this, 'save_meta_data' ) );
		
		add_action( 'wp_ajax_get_copyright', array( $this, 'ajax_get_copyright' ) );
		add_action( 'wp_ajax_nopriv_get_copyright', array( $this, 'ajax_get_copyright' ) );
		add_action( 'wp_footer', array( $this, 'ajax_script' ) );
		add_action( 'init', array( $this, 'load_hello_textdomain' ) );
		add_filter( 'woocommerce_product_tabs', array( $this, 'custom_product_tabs' ) );
		add_action( 'woocommerce_single_product_summary', array( $this, 'custom_product_title' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_custom_style' ) );
		add_filter( 'manage_users_columns', array( $this, 'new_modify_user_table' ) );
		add_filter( 'manage_users_custom_column', array( $this, 'new_modify_user_table_row' ), 10, 3 );
		add_action( 'init', array( $this, 'custom_post_type' ) );
	}

	//文章内容里添加版权
	function add_copyright( $content ) {
		if( is_singular() ){
			global $post;
			$copyright = get_post_meta( $post->ID, 'copyright_text', true );
			$copyright_text = $copyright ? $copyright : get_option('copyright_text');
			$content .= $copyright_text;
		}

		return $content;
	}


	//右下角里显示一下文字，类似 hello Dolly
	function hello_world_get_words() {
	    $greetings = array(
	        'Hello World',
	        'How’s it going?',
	        'What’s up?',
	        'How’s everything?',
	        'Nice to see you',
	        'Long time no see',
	        'How do you do?',
	        'Are you OK?',
	        'Yo!',
	        'Howdy!',
	        'Hiya!',
	        'G’day mate!',
	    );

	    $greetings = apply_filters( 'hello_greetings', $greetings );

	    return wptexturize( $greetings[ mt_rand( 0, count( $greetings ) - 1 ) ] );
	}

	function hello_world() {
	    $chosen = $this->hello_world_get_words();
	    echo "<p id='world'>" . get_bloginfo( 'name', 'display' ) . " 跟你说：{$chosen}</p>";
	}



	function hello_world_css() {
	    echo "
	    <style type='text/css'>
	    #world {
	        position: fixed;
	        bottom: 0;
	        right:0;
	        background-color:#f8f8f8;     
	        padding:15px 20px;
	        margin: 0;
	        font-size: 15px;
	        z-index:1000;
	        border:1px soild #999;
	        border-radius: 4px;
	        color: #da4453;
	    }
	    </style>
	    ";
	}



	// //顶级子菜单
	// function test_function(){   
	//   add_menu_page( 'title标题', '菜单标题', 'edit_themes', 'ashu_slug','display_function','',6);   
	// }   
	    
	// function display_function(){   
	//   echo '<h1>这是设置页面</h1>';   
	// }   
	// add_action('admin_menu', 'test_function');  


	  
	// //以下是添加子菜单项代码  
	// add_action('admin_menu', 'add_my_custom_submenu_page');  
	  
	// function add_my_custom_submenu_page() {  
	//   //顶级菜单的slug是ashu_slug  
	//   add_submenu_page( 'ashu_slug', '子菜单', '子菜单', 'edit_themes', 'ashu-submenu-page', 'my_submenu_page_display' );  
	// }  
	  
	// function my_submenu_page_display() {  
	//   echo '<h3>子菜单项的输出代码</h3>';  
	  
	// }

	function shortcode_test( $atts, $content ){
		$attr = shortcode_atts(array(
			'num1' => 0,
			'num2' => 0
		), $atts, 'add_num');
		return do_shortcode($content) . ( (int)$attr['num1'] + (int)$attr['num2'] );
	}

	//添加设置的子菜单--版权设置



	function  add_copyright_menu(){
		add_submenu_page('options-general.php','版权设置','版权设置','manage_options','copyright-menu','add_copyright_menu_cb');
	}

	//版权设置页面添加表单
	function add_copyright_menu_cb() {
		$settings = get_option('copyright_text');
	 ?>

	  <div class="wrap">
		<h2><?php _e('Copyright','hello-test') ?></h2>
		
		<form method="post" action="options.php">
			<?php wp_nonce_field('update-options') ?>

			<table class="form-table">
			<tr><th><label for="copyright_text">版权文字</label></th>
			<td>
			<textarea id="copyright_text" name="copyright_text"><?php echo $settings ?></textarea>
			</td></tr>
			</table>
			
			<p class="submit">
				<input type="hidden" name="action" value="update">
				<input type="hidden" name="page_options" value="copyright_text">
				<input type="submit" value="保存" class="button-primary save-btn">
			</p>
		</form>
	</div>

	 <?php 

	}

	//
	function wporg_add_custom_box()
	{
	        add_meta_box(
	            'wporg_box_id',           // Unique ID
	            'Custom Meta Box Title',  // Box title
	            'wporg_custom_box_html',  // Content callback, must be of type callable
	            'post'                   // Post type
	        );
	}

	function wporg_custom_box_html(){
		global $post;
		$copyright = get_post_meta( $post->ID, 'copyright_text', true );
	?>
	<?php wp_nonce_field( 'save_copyright', 'copyright_nonce' ); ?>
		<table class="form-table">
		<tr><th><label for="copyright_text">版权文字</label></th>
		<td>
		<textarea id="copyright_text" name="copyright_text"><?php echo $copyright ?></textarea>
		</td></tr>
		</table>
	<?php
	}

	//

	function save_meta_data( $post_id ){
		//验证
		if( ! isset($_POST['copyright_nonce']) || ! wp_verify_nonce( $_POST['copyright_nonce'], 'save_copyright' ) ){
			return $post_id;
		}

		//自动保存检查
		if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
			return $post_id;
		}

		if( 'post' != $_POST['post_type'] ){
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

		update_post_meta( $post_id, 'copyright_text', $_POST['copyright_text'] );
	}

	//ajax版权

	function ajax_get_copyright(){
		$post_id = $_REQUEST['post_id'];
		$copyright = get_post_meta( $post_id, 'copyright_text', true );
		$post_title = get_post($post_id)->post_title;
		echo json_encode(array(
			'copyright' => $copyright,
			'post_title' => $post_title
		));
		wp_die();
		exit;
	}


	function ajax_script(){
		global $post;
	?>
	<script>
	(function($){
	$.ajax({
		url: '<?php echo admin_url('admin-ajax.php') ?>',
		type: 'post',
		dataType: 'json',
		data:{
			action: 'get_copyright',
			post_id: <?php echo $post->ID ?>
		},
		success: function(data){
			alert(data.post_title);
		}
	});
	})(jQuery)
	</script>

	<?php
	// ajax 的打印测试
	// 数据库存在 wp-postmeta
	// http://localhost/wordpress/wp-admin/admin-ajax.php?action=get_copyright&post_id=1
	}


	function load_hello_textdomain(){
		load_plugin_textdomain( 'hello-test', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	//add_action( 'admin_notices', 'show_salt' );
	function show_salt(){
		$response = wp_remote_get('https://api.wordpress.org/secret-key/1.1/salt/');
		if( ! is_wp_error( $response ) ){
			echo $response['body'];
		}
	}

	function custom_product_tabs( $tabs ){
		$tabs['attr'] = array(
			'title' => '属性',
			'priority' => 15,
			'callback' => 'attr_product_tab_content'
		);
		return $tabs;
	}

	function attr_product_tab_content(){
		echo 'asd';
	}


	function custom_product_title(){
		?>
		<p class="asd">test</p>
		<?php
	}


	function add_custom_style(){
		wp_enqueue_style( 'custom-style', plugin_dir_url( __FILE__ ) . 'assets/css/style.css' );
	}


	function new_modify_user_table( $columns ) {
	    $columns['reg_date'] = '注册时间';
	    return $columns;
	}

	function new_modify_user_table_row( $val, $column_name, $user_id ) {
	    switch ($column_name) {
	        case 'reg_date' :
	            $udata = get_userdata( $user_id );
	            //return  date_i18n( 'Y-m-d H:i:s', strtotime('2018-03-05 02:11:59'), true );
	             return  date('Y-m-d H:i:s',strtotime( $udata->user_registered ) ) ;
	            //return get_gmt_from_date( strtotime('2018-03-05 02:11:59'), $format = 'Y-m-d H:i:s');
	            break;
	        default:
	    }
	    return $val;
	} 

	function custom_post_type(){
		$args =  array(
           'labels'      => [
               'name'          => __('Music'),
               'singular_name' => __('Music'),
           ],
           'public'      => true,
           'has_archive' => true,
           'menu_icon' => 'dashicons-format-audio',
           'supports' => array(
           		'title',
           		'editor',
           		'thumbnail'
           	)
        );
		register_post_type( 'music', $args );
	}

}


new Test_Plugin();
?>

