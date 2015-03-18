<?php
/**
 * @package sc_editor
 * @version 1
 */
/*
Plugin Name: Semantic Content Editor
Plugin URI: 
Description: A WordPress plugin for building cleanly coded posts from predefined blocks and markdown.
Version: 1.0
Author: Christian Gloss, Nick Noble, Gary bacon
Author URI: github.com/nickisnoble/Semantic-Content-Editor
*/

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
	//print_pre($sorted);
	uasort($sorted, "obj_sort_order");
	return (object)$sorted;
}

function sce_init() {
	//setup

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
					'id'    	=> 'markdown_boxes',  
					'type'  	=> 'multibox',
					'posttype'  => 'section',
					'desc'		=> 'Markdown input box',
					'std'		=> 'markdown content'
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
	     
	    // Use nonce for verification
	    echo '<input type="hidden" name="sce_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
	     
	    echo '<table class="form-table">';
	     
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
			// WIP chk
			//echo '<pre>';
			//print_r($sorted);
			//echo '</pre>';
			foreach($sorted as $k => $v){
				?>
				<tr style="border-bottom:1pt dashed #eee;" class="<?php echo $post->ID; ?>">
	                
	                <input class="sce_box order" type="hidden" name="<?php echo $field['type'].'_'.$field['id'].'_order_'.$k;?>" value="<?php echo $v->order!=NULL ? $v->order : ''; ?>">
	                <td>
	                    <span style="height:60px; display:block;">
	                    	<a href="#" id="<?php echo $field['type'].'_'.$field['id'].'_#_'.$k;?>" class="button delmulti_media">X</a>
	                	</span>
	                <?php
					echo '<textarea name="', $field['type'].'_'.$field['id'].'_sceeditor_'.$k, '" id="', $field['type'].'_'.$field['id'].'_sceeditor_'.$k, '" cols="60" rows="4" style="width:97%">', $v ? $v : $field['std'], '</textarea>',
					'<br />', $field['desc'];
					?>	
	                
	                </td>
	        	</tr>
		<?php } //end foreach ?> 		
	            <tr>
	            	<td>
	            		<a href="#" id="<?php echo $field['type'].'_'.$field['id'];?>" data-filetype="<?php echo $field['filetype'];?>" class="button addmulti_media">+Add a content box</a>
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
	     
	    echo '</table>';
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
	     
	   /* foreach ($this->_meta_box['fields'] as $field) {
	    $old = get_post_meta($post_id, $field['id'], true);
		$new = $_POST[$field['id']];*/
		
		foreach ($this->_meta_box['fields'] as $field) {
	    $old = get_post_meta($post_id, $field['id'], true);
	    if(!is_array($_POST[$field['id']])){
	    $new = html_entity_decode($_POST[$field['id']]);
		}else{
	    $new = $_POST[$field['id']];
		}
	    
		// if _datetime convert to timestamp
		if ($field['type'] == 'datetime' && DateTime::createFromFormat('Y, M d', $_POST[$field['id']])) {
		$date = DateTime::createFromFormat('Y, M d', $_POST[$field['id']]);
		$new = $date->format('Y-m-d');
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

// CSS  
function sce_box_css(){
	global $typenow; 
	if ( 'post.php' || 'post-new.php' || $typenow == 'post' ) {
  ?>
	
	<style type="text/css">
		.image-uploader-meta-box-list:after{
		  display:block;
		  content:'';
		  clear:both;	
		}
		.image-uploader-meta-box-list li {
		  float: left;
		  width: 150px;
		  height:auto;
		  text-align: center;
		  margin: 10px 10px 10px 0;
		}
		input.sce_box{
			width:50%;
		}
		.image-uploader-meta-box-list li img{
			max-width:150px;
		}
		a.sce_box-add.none, a.change-image.none, a.remove-image.none, input.sce_box.none{
			display:none;
			visibility:hidden;
		}
	</style>
	
  <?php }
  }
add_action('admin_head', 'sce_box_css');


function sce_output() {
	//build
}
?>
