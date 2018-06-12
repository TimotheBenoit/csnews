<?php
/*
Plugin Name: CS WP News
Description: FHIV IV FHE BEFT PLUGIN YOU'FE EFER FEEN.
Author: Aplui
*/


// Include cs-functions.php, use require_once to stop the script if cs-functions.php is not found
require_once plugin_dir_path(__FILE__) . 'includes/cs-functions.php';

add_shortcode( 'shortcode-news', 'display_custom_post_type' );

function display_custom_post_type(){

    $string = "<div class='carousel'>";
    $nbpost = get_number_posts();
    $nbpost = intval($nbpost[0]->nb_post);
    $count_posts = wp_count_posts('csnews');
    $intCount = intval($count_posts->publish);
    if( $nbpost > $intCount || !isset($nbpost)) {
      $nbpost = -1;
    }
    $args = array("posts_per_page" => $nbpost, "post_type" => "csnews");
    $posts_array = get_posts($args);

    foreach($posts_array as $post) {
        setup_postdata($post);

        // Return the type of the post and its parameters (number of posts diplayed)
        $temp = get_post_meta($post->ID);

        $start = $temp[custom_DateStart][0]; // When the post starts
        $end = $temp[custom_DateEnd][0]; // When the post ends
        $category = $temp[custom_cat][0];
        $img = get_the_post_thumbnail_url($post->ID);

        // We define the date of the day to compare
        $currentDate = new DateTime($currentDate);
	      $currentDate = $currentDate->format('d-m-Y'); // Change if you don't register the date like this in your db

        // If dates are correct or if dates are empty we display this, otherwise we don't
        if($start < $currentDate && $currentDate < $end || empty($start) && $end > $currentDate || empty($start) && empty($end) || empty($end) && $start < $currentDate){
          if($temp[custom_select][0] == "video") {
              $string .= "
            <div class='carousel__content' style='background:url(". $img .") no-repeat;'>
              <div class='carousel__box'>"; ?>

              <?php if(!empty($category)) {
                $string .= "
                <h1 class='carousel__category'>". $category ."</h1>";
               }
                $string .= "
                <h1 class='carousel__title'>". $post->post_title ."</h1>
                <div>" . $post->post_content . "</div>
                <div class='box'>
                  <div class='box__content'>
                    <a href='https://www.youtube.com/watch?v=". $temp[custom_text][0] ."' target='_blank' class='hvr-reveal'>Regarder</a>
                  </div>
                </div>
              </div>
            </div><!-- carousel__content -->"; ?>

          <?php } else {
            $string .= "
            <div class='carousel__content' style='background:url(". $img .") no-repeat;'>
              <div class='carousel__box'>" ;?>

                <?php if(!empty($category)) {
                  $string .= "
                <h1 class='carousel__category'>". $category ."</h1>";
                }
                $string .= "
                <h1 class='carousel__title'>". $post->post_title ."</h1>
                <div>" . $post->post_content . "</div>
                <div class='box'>
                  <div class='box__content'>
                    <a href='#' target='_blank' class='hvr-reveal'>Écouter</a>
                  </div>
                  <div class='box__content'>
                    <a href='#' class='hvr-reveal'>Commander</a>
                  </div>
                </div>
              </div>
            </div><!-- carousel__content -->"; ?>
        <?php }
        }
    }
    $string .= "
          </div><!-- carousel -->
          ";
    wp_reset_postdata();
    return $string;
}


/* ----------------------------------------------------------
************************** FUNCTIONS ************************
------------------------------------------------------------- */


function get_number_posts() {

  global $wpdb;

  $nb_post = $wpdb->get_results("SELECT option_value AS nb_post FROM {$wpdb->prefix}options WHERE option_name='news_per_carousel'");

  return $nb_post ;
}
/*
function get_custom_type($id) {

  global $wpdb;

  $type = $wpdb->get_results("SELECT meta_value AS type FROM {$wpdb->prefix}postmeta WHERE post_id=$id AND meta_key='custom_select'");
  $nb_post = $wpdb->get_results("SELECT option_value AS nb_post FROM {$wpdb->prefix}options WHERE option_name='news_per_carousel'");
  $link = $wpdb->get_results("SELECT meta_value AS link FROM {$wpdb->prefix}postmeta WHERE post_id=$id AND meta_key='custom_text'");
  return array($type, $link, $nb_post);
}

function get_custom_dates($id) {

  global $wpdb;

  $dateStart = $wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id=$id AND meta_key='custom_DateStart'");
  $dateEnd = $wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id=$id AND meta_key='custom_DateEnd'");

  return array( $dateStart, $dateEnd);
}
