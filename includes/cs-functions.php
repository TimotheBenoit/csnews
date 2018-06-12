<?php

function myplugin_register_settings() {
   add_option( 'news_per_carousel', 'This is my option value.');
   register_setting( 'myplugin_options_group', 'news_per_carousel', 'myplugin_callback' );
}
add_action( 'admin_init', 'myplugin_register_settings' );

function myplugin_register_options_page() {
  add_options_page('Page Title', 'Menu CS News', 'manage_options', 'myplugin', 'myplugin_options_page');
}
add_action('admin_menu', 'myplugin_register_options_page');

function myplugin_options_page()
{
?>
  <div>
    <?php screen_icon(); ?>
    <h2>Menu CS News</h2>
    <form method="post" action="options.php">
      <?php settings_fields( 'myplugin_options_group' ); ?>
      <p>Options concernant le carousel.</p>
      <table>
        <tr valign="top">
          <th scope="row"><label for="myplugin_option_name">Nombre de news à montrer : </label></th>
          <td><input type="number" id="myplugin_option_name" name="news_per_carousel" value="5" /></td>
        </tr>
      </table>
      <?php  submit_button(); ?>
    </form>
  </div>
<?php
}



add_theme_support( 'post-thumbnails' );

 // Add the Meta Box
 function add_custom_meta_box() {
     add_meta_box(
         'custom_meta_box', // $id
         'Informations complémentaires', // $title
         'show_custom_meta_box', // $callback
         'csnews', // $page
         'normal', // $context
         'high'); // $priority
 }
 add_action('add_meta_boxes', 'add_custom_meta_box', 0);

 // Field Array
$prefix = 'custom_';
$custom_meta_fields = array(
		array(
				'label' => 'Nature de la publication',
				'desc'  => 'Précisez la nature de la publication.',
				'id'    => $prefix.'select',
				'type'  => 'select',
				'options' => array (
						'one' => array (
							'label' => 'Image',
							'value' => 'image'
						),
						'two' => array (
							'label' => 'Vidéo',
							'value' => 'video'
						)
				)
		),
    array(
        'label' => 'www.youtube.com/watch?v=',
        'desc'  => 'UNIQUEMENT L\'ID DE LA VIDEO',
        'id'    => $prefix.'text',
        'type'  => 'videoID'
    ),
		array(
			'label' => 'Date de début',
			'desc'  => 'Si non précisé, prend effet suite à la publication.',
			'id'    => $prefix.'dateStart',
			'type'  => 'date'
		),
		array(
			'label' => 'Date de fin',
			'desc'  => 'Si non précisé, elle sera tout le temps affichée.',
			'id'    => $prefix.'DateEnd',
			'type'  => 'date'
		),
    array(
			'label' => 'Thème',
			'desc'  => 'Musique, concert, événement...',
			'id'    => $prefix.'cat',
			'type'  => 'category'
		)
);

// The Callback
function show_custom_meta_box() {
global $custom_meta_fields, $post;
// Use nonce for verification
echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';

    // Begin the field table and loop
    echo '<table class="form-table">';
    foreach ($custom_meta_fields as $field) {
        // get value of this field if it exists for this post
        $meta = get_post_meta($post->ID, $field['id'], true);
        // begin a table row with
        echo '<tr>
                <th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
                <td>';
                switch($field['type']) {
									// select
									case 'select':
									    echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
									    foreach ($field['options'] as $option) {
									        echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
									    }
									    echo '</select><br /><span class="description">'.$field['desc'].'</span>';
									break;
									// text
									case 'videoID':
										echo '<input type="text" class="seen" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />
												<br /><span class="description">'.$field['desc'].'</span>';
									break;
                  // date
                  case 'date':
										echo '<input type="text" class="datepicker" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />
												<br /><span class="description">'.$field['desc'].'</span>';
									break;
                  // category
                  case 'category':
										echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />
												<br /><span class="description">'.$field['desc'].'</span>';
									break;

                } //end switch
        echo '</td></tr>';
    } // end foreach
    echo '</table>'; // end table
}

// Save the Data
function save_custom_meta($post_id) {
    global $custom_meta_fields;

    // verify nonce
    if (!wp_verify_nonce($_POST['custom_meta_box_nonce'], basename(__FILE__)))
        return $post_id;
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;
    // check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return $post_id;
        } elseif (!current_user_can('edit_post', $post_id)) {
            return $post_id;
    }

    // loop through fields and save the data
    foreach ($custom_meta_fields as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = $_POST[$field['id']];
        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    } // end foreach
}
add_action('save_post', 'save_custom_meta');

function register_datepiker_submenu() {
    add_submenu_page( 'options-general.php', 'Date Picker', 'Date Picker', 'manage_options', 'date-picker', 'datepiker_submenu_callback' );
}

function datepiker_submenu_callback() {

}
add_action('admin_menu', 'register_datepiker_submenu');







function wpm_custom_post_type() {

	// On rentre les différentes dénominations de notre custom post type qui seront affichées dans l'administration
	$labels = array(
		// Le nom au pluriel
		'name'                => _x( 'CS News', 'Post Type General Name'),
		// Le nom au singulier
		'singular_name'       => _x( 'CS New', 'Post Type Singular Name'),
		// Le libellé affiché dans le menu
		'menu_name'           => __( 'CS News'),
		// Les différents libellés de l'administration
		'all_items'           => __( 'Tout les news'),
		'view_item'           => __( 'Voir les news'),
		'add_new_item'        => __( 'Ajouter un nouvelle news'),
		'add_new'             => __( 'Ajouter'),
		'edit_item'           => __( 'Editer le news'),
		'update_item'         => __( 'Modifier le news'),
		'search_items'        => __( 'Rechercher un news'),
		'not_found'           => __( 'Non trouvée'),
		'not_found_in_trash'  => __( 'Non trouvée dans la corbeille'),
	);

	// On peut définir ici d'autres options pour notre custom post type

	$args = array(
		'label'               => __( 'CS News'),
		'description'         => __( 'Tous sur les news'),
		'labels'              => $labels,
		// On définit les options disponibles dans l'éditeur de notre custom post type ( un titre, un auteur...)
		'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes'),
		'register_meta_box_cb' => 'add_custom_meta_box',
		/*
		* Différentes options supplémentaires
		*/
		'hierarchical'        => false,
		'public'              => true,
		'has_archive'         => true,
		'rewrite'			  => array( 'slug' => 'cs-news'),

	);

	// On enregistre notre custom post type qu'on nomme ici "csnews" et ses arguments
	register_post_type( 'csnews', $args );

}

add_action( 'init', 'wpm_custom_post_type', 0 );



/*
--------------------------- SCRIPTS AND STYLES ------------------------------
*/

add_action( 'wp_enqueue_scripts', 'slick_enqueue_script' );
add_action( 'wp_enqueue_scripts', 'custom_enqueue_script' );
add_action( 'admin_enqueue_scripts', 'custom_admin_enqueue_script');
add_action( 'wp_print_styles', 'custom_enqueue_styles' );

function slick_enqueue_script() {
  wp_enqueue_script( 'document-slider-script', plugins_url('slick/slick.js' ,(__FILE__)), array('jquery'));
}
function custom_enqueue_script() {
  wp_enqueue_script( 'document-personal-script', plugins_url('js/app.js' ,(__FILE__)), array('jquery'));
}
function custom_admin_enqueue_script() {
  wp_enqueue_script( 'document-date-script', plugins_url('js/date.js' ,(__FILE__)), array('jquery'));
}
function custom_enqueue_styles() {
  wp_enqueue_style( 'document-attached-style', plugins_url('css/style.css' ,(__FILE__)));
}
function add_e2_date_picker(){
  //jQuery UI date picker file
  wp_enqueue_script('jquery-ui-datepicker');
  //jQuery UI theme css file
  wp_enqueue_style('e2b-admin-ui-css','http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/base/jquery-ui.css',false,"1.9.0",false);
  }
add_action('admin_enqueue_scripts', 'add_e2_date_picker');
