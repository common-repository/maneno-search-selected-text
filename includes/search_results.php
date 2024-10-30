<?php

// query to get all published posts

/*$query="SELECT * FROM $wpdb->posts WHERE post_status =
'publish' AND post_password='' ORDER BY post_date_gmt DESC ";
$posts = $wpdb->get_results($query);*/

if (!defined('WP_PLUGIN_URL')) {
// WP functions become available once you include the config file
require_once( realpath('../../../../').'/wp-config.php' );
}

if ( empty($_POST['s']) )
{
exit;
}
$max_posts = (int) $_POST['m'];
$searchindexval = (int) $_POST['searchindexval'];
$postID = (int) $_POST['postID'];

$offset = $searchindexval * $max_posts;


if(!$max_posts) $max_posts = 1;
$WP_Query_object = new WP_Query();
$WP_Query_object->query(array('s' => $_POST['s'], 'posts_per_page' => $max_posts, 'offset' => $offset, 'post__not_in' => array($postID), 'post_type' => array('post','page')));

$WP_Query = new WP_Query();
$WP_Query->query(array('s' => $_POST['s'], 'post__not_in' => array($postID), 'post_type' => array('post','page')));
$totnbrpost = $WP_Query->post_count;


$nbrposts_per_page = $max_posts;

$nbrpagesval = (int) ($totnbrpost / $nbrposts_per_page);
if($totnbrpost%$nbrposts_per_page) $nbrpagesval++;


while( @ob_end_clean() );
header('Content-type: text/xml');
echo "<?xml version='1.0' encoding='utf-8'?>";
echo "<searchresponse>";
echo "<posts>";
echo "<![CDATA[";						
echo '<div id="page_'.$searchindexval.'" class="postpage">';	

					
foreach($WP_Query_object->posts as $result)
{
//print_r($result);

		$post_content = substr(strip_tags($result->post_content),0,get_option('result_char_number'));
		$permalink = get_permalink( $result->ID );
		echo '<p class="postresult"><strong>'.$result->post_title.'</strong><br>'.$post_content.'... <a href="'.$permalink.'" class="readmore">Read more</a></p>';
}
echo '</div>';	
echo "]]>";
echo "</posts>";					
echo "<postsnbr>";
echo $totnbrpost;
echo "</postsnbr>";
echo "<nbrpagesval>";
echo $nbrpagesval;
echo "</nbrpagesval>";
echo "</searchresponse>";
/* EOF */
?>