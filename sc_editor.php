<?php
/**
 * @package sc_editor
 * @version .01
 */
/*
Plugin Name: Semantic Content Editor
Plugin URI: 
Description: A WordPress plugin for building cleanly coded posts from predefined blocks and markdown.
Version: .01
Author: Christian Gloss, Nick Noble, Gary bacon
Author URI: github.com/nickisnoble/Semantic-Content-Editor
*/

// helper sort object by order
function obj_sort_order($a, $b) {
	return strnatcmp($a->order, $b->order);
}

// helper func to build the ordered product multibox object
function sort_multibox($id,$meta){
	$thismeta = get_post_meta($id);
	
	error_reporting(E_ERROR);
	$mfiles=new stdClass();
	
	foreach($thismeta as $k => $v){
		if (strpos($k,$meta) !== false) {
			$mfile = explode("_", $k);										
			$mfiles->$mfile[1]->$mfile[3]->$mfile[2]=$v[0];	
		}
	}
	
	$sorted = get_object_vars($mfiles->$meta);

	uasort($sorted, "obj_sort_order");
	return (object)$sorted;
}

function sce_init() {
	//setup

	wp_enqueue_script('sce_admin_js', plugin_dir_url( __FILE__ ) . 'lib/js/sc_admin.js', array('jquery'), NULL, true);
	
	/**
	 * Register meta box.
	 *
	 */
	$meta_boxes = array(
		array(
			'id' => 'sce_markdown_boxes',
			'title' => 'Markdown Boxes',
			'pages' => array('post','page'),
			'context' => 'normal',
			'priority' => 'high',
			'fields' => array(
				array(  
					'name'		=> 'Markdown',
					'id'    	=> 'themarkdownboxes',  
					'type'  	=> 'multibox',
					'posttype'  => 'section',
					'desc'		=> 'Markdown input box'
				)
			)
		),
		
	);

	// setup our meta box class
	class sce_meta_box {
	     
	    protected $_meta_box;
	     
	    // create meta box based on given data
	    function __construct($meta_box) {
	    $this->_meta_box = $meta_box;
	    add_action('admin_menu', array(&$this, 'add'));
	     
	    add_action('save_post', array(&$this, 'save'));
	    }
	     
	    // Add meta box for multiple post types
	    function add() {
	    foreach ($this->_meta_box['pages'] as $page) {
	    add_meta_box($this->_meta_box['id'], $this->_meta_box['title'], array(&$this, 'show'), $page, $this->_meta_box['context'], $this->_meta_box['priority']);
	    }
	    }
	     
	    // Callback function to show fields in meta box
	    function show() {
	    global $post;
	    
	    // $meta = get_post_meta($post->ID);
	    // print_r($meta);


	    // Use nonce for verification
	    echo '<input type="hidden" name="sce_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
	     
	    echo '<table class="form-table sce"><tbody>';
	    
	    foreach ($this->_meta_box['fields'] as $field) {
	    // get current post meta data
	    $meta = get_post_meta($post->ID, $field['id'], true);
	     
	    if(!in_array($field['type'], array('multibox','posts_select'), true )){
		echo '<tr>',
		'<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th></tr>',
		'<tr><td>';
		}
	    switch ($field['type']) {	
			// multibox  
			case 'multibox':	
			$sorted = sort_multibox($post->ID,$field['id']);
			uasort($sorted, "obj_sort");

			foreach($sorted as $k => $v){
				?>
				<tr data-pid="<?php echo $post->ID;?>" class="sce_tr">
	                
	                <input class="sce_box order" type="hidden" name="<?php echo $field['type'].'_'.$field['id'].'_order_'.$k;?>" value="<?php echo $v->order!=NULL ? $v->order : ''; ?>">
	                <td>
	                    <span>
	                    	<a href="#" id="<?php echo $field['type'].'_'.$field['id'].'_#_'.$k;?>" class="button delmulti_box">X</a>
	                	</span>
	                <?php
					echo '<textarea class="multibox" name="', $field['type'].'_'.$field['id'].'_sceeditor_'.$k, '" id="', $field['type'].'_'.$field['id'].'_sceeditor_'.$k, '" cols="60" rows="4">', $v->sceeditor,'</textarea>',
					'<br />', $field['desc'];
					?>	
	                
	                </td>
	        	</tr>
		<?php } //end foreach ?> 		
	            <tr>
	            	<td>
	            		<a href="#" id="<?php echo $field['type'].'_'.$field['id'];?>" data-filetype="<?php echo $field['filetype'];?>" class="button addmarkdown_box">+Add a content box</a>
	            	</td>
	           </tr>
			<?php
			break;
	    }
		if($field['type'] != 'multibox'){
		    echo '</td>',
		    '</tr>';
		}
	    }
	     
	    echo '</tbody></table>';
	    }
		     
	    // Save data from meta box
	    function save($post_id) {
	    // verify nonce
	    if (!wp_verify_nonce($_POST['sce_meta_box_nonce'], basename(__FILE__))) {
	    return $post_id;
	    }
	     
	    // check autosave
	    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
	    return $post_id;
	    }
	     
	    // check permissions
	    if ('page' == $_POST['post_type']) {
	    if (!current_user_can('edit_page', $post_id)) {
	    return $post_id;
	    }
	    } elseif (!current_user_can('edit_post', $post_id)) {
	    return $post_id;
	    }
	     		
		foreach ($this->_meta_box['fields'] as $field) {
	    $old = get_post_meta($post_id, $field['id'], true);
	    if(!is_array($_POST[$field['id']])){
	    $new = html_entity_decode($_POST[$field['id']]);
		}else{
	    $new = $_POST[$field['id']];
		}
	      
	    if ($new && $new != $old) {
	    update_post_meta($post_id, $field['id'], $new);
	    } elseif ('' == $new && $old) {
			delete_post_meta($post_id, $field['id'], $old);
	    }	
	    }
	    }
	}

	if($meta_boxes){
		foreach ($meta_boxes as $meta_box) {
			$sce_box = new sce_meta_box($meta_box);
		}
	}
}
// add action for init
add_action( 'init', 'sce_init' );

// save meta->meta data
function sce_meta_save($post_id) {
	if (!current_user_can('edit_post', $post_id)) return $post_id;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	foreach($_POST as $key => $value){
		$exp_key = explode('_', $key, 3);
		if($exp_key[1] == 'multibox'){
		update_post_meta($post_id, $exp_key[1].'_'.$exp_key[2], esc_attr($value)); // meta id created here
		}
		if($exp_key[0] == 'multibox'){
		update_post_meta($post_id, $key, esc_attr($value)); // meta id created here
		}
	}
}

add_action('save_post', 'sce_meta_save');

// CSS  
function sce_box_css(){
	global $typenow; 
	if ( 'post.php' || 'post-new.php' || $typenow == 'post' ) {
		wp_enqueue_style('sce_admin_css', plugin_dir_url( __FILE__ ) . 'lib/css/sc_admin.css');
 	}
}
add_action( 'admin_print_styles','sce_box_css' );

//wp admin ajax api functions
function delmeta_callback() {
    global $wpdb; // db access

    if(isset($_REQUEST['delmeta'])){
		$arr = array('order','sceeditor');
		foreach($arr as $v){
			$metaID = str_replace("#", $v, $_REQUEST['delmeta']);
			delete_post_meta($_REQUEST['postID'], $metaID);
			echo $metaID;
		}
	}
    die();
}
add_action('wp_ajax_delmeta', 'delmeta_callback');

function sce_output() {
	//build
}
?>