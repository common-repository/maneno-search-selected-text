<?php
/*
Plugin Name: Maneno - Search selected text
Plugin URI: http://www.sourcecreativity.com/
Description: This plugin allows you to highlight text on a post or a page then launch a search for the selected text on the website.
Author: Source Creativity LLC
Author URI: http://www.sourcecreativity.com/
Version: 1.0.2
*/

/*  Copyright 2011 Source Creativity LLC

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

define('PLUGIN_URL', plugin_dir_url( __FILE__ ));

add_action( 'admin_menu', 'maneno_register_settings_page' );

function maneno_register_settings_page() {
	add_options_page('Maneno', 'Maneno', 'read', basename(__FILE__), 'maneno_config_page' );
}

function maneno_config_page() {

if ( isset($_POST['submit']) ) {
	$options = array();
	$_POST['searchicon'] = ($_POST['searchicon']=="")?'search-1.png':$_POST['searchicon'];
	$_POST['result_char_number'] = ($_POST['result_char_number']=="")?'300':$_POST['result_char_number'];
	$_POST['result_number'] = ($_POST['result_number']=="")?'2':$_POST['result_number'];
	$_POST['min_char'] = ($_POST['min_char']=="")?'2':$_POST['min_char'];
	update_option('searchicon', $_POST['searchicon']);
	update_option('result_number', $_POST['result_number']);
	update_option('min_char', $_POST['min_char']);
	update_option('result_char_number', $_POST['result_char_number']);
	echo '<div class="updated fade"><p>Plugin settings saved.</p></div>';
}
	?>
	<div class="wrap">
	<?php screen_icon(); ?>
	<h2>Maneno Administration</h2>
	<?php print $msg; ?>
	<form action="" method="post">
	<h3><label for="separator">Search icon</label></h3>
	<p>Select the icon displayed when you select a text<br/>
	</p>
	
	<select name="searchicon" id="searchicon">
	<?php 
	$dir = opendir(ABSPATH. 'wp-content/plugins/maneno-search-selected-text/images/search_icons');
	
	//List files in images directory
	$exts = array('.jpg','.png','.gif');
	while (($file = readdir($dir)) !== false) {	
		$ext = strrchr($file,'.');
		if($file!='..' && $file!='.' && in_array($ext,$exts)) {
			if($file == get_option('searchicon'))  
				$selected = ' selected="selected"';
			echo '<option value="'.$file.'"'.$selected .'>'.$file.'</option>';
			
		}
		$selected = '';
	  }
	
	closedir($dir);
	
	$result_number = (get_option('result_number')=="")?'2':get_option('result_number');
	$min_char = (get_option('min_char')=="")?'2':get_option('min_char');
	$result_char_number = (get_option('result_char_number')=="")?'400':get_option('result_char_number');
	
	?></select>
    <h3><label for="content_block">Results number</label></h3>
    <p>
    Enter the number of posts displayed in the result <br/>
    <input type="text" id="result_number" name="result_number" size="2" value="<?php print $result_number; ?>" />
    </p>
    <h3><label for="content_block">Minimum searched characters</label></h3>
    <p>
    Enter the minimum number of characters of the search words <br/>
    <input type="text" id="min_char" name="min_char"  size="3" value="<?php print $min_char; ?>" />
    </p>
    <h3>
      <label for="content_block">Characters per post</label>
    </h3>
    <p>
    Enter the number of characters per post displayed in the result <br/>
    <input type="text" id="result_char_number" name="result_char_number" size="4" value="<?php print $result_char_number; ?>" />
    </p>
    <p class="submit"><input type="submit" name="submit" value="Update" /></p>
	
    </form>
	<a href="http://www.sourcecreativity.com" style="text-decoration:none" >Source Creativity</a>
	</div>
	<?php		
}

function maneno_jquery_register() {
	if ( !is_admin() ) {
		 wp_enqueue_script( 'jquery', ( PLUGIN_URL . 'js/jquery-1.5.1.min.js' ), array("jquery") );
		 wp_enqueue_script( 'jquery_highlight', ( PLUGIN_URL . 'js/jquery.highlight-3.js' ), array("jquery") );
		 wp_enqueue_style('paginate-css', PLUGIN_URL . 'css/maneno.css');
	}
}

add_action( 'init', 'maneno_jquery_register' );

function Maneno_Head(){
global $post;

echo '<script type="text/javascript">
var selectionexists = 0;
var IE = document.all?true:false;
if (!IE) document.captureEvents(Event.MOUSEUP)


document.onmouseup = getMouseXY;
var tempX = 0;
var tempY = 0;

function getMouseXY(e) {
if (IE) { // grab the x-y pos.s if browser is IE
tempX = event.clientX + document.body.scrollLeft;
tempY = event.clientY + document.body.scrollTop;
}
else {  // grab the x-y pos.s if browser is NS
tempX = e.pageX;
tempY = e.pageY;
}  
if (tempX < 0){tempX = 0;}
if (tempY < 0){tempY = 0;}  
/* alert(tempX +\' \'+ tempY); */
return true;
}
//  End -->

//////////////

if(!window.sc){
  sc = {};
}

sc.Selector = {};
sc.Selector.getSelected = function(){
  var t = \'\';
  if(window.getSelection){
    t = window.getSelection();
  }else if(document.getSelection){
    t = document.getSelection();
  }else if(document.selection){
    t = document.selection.createRange().text;
  }
  return t;
}
sc.Selector.mouseup = function(event){
  var st = sc.Selector.getSelected();
  var selectionval = st.toString();
  document.Show.selectionexists.value = selectionval.length;
  document.Show.selectionval.value = selectionval;
  
  if(selectionval.length!=0 && event.target.className!= \'neno_search\'){
	jQuery(".neno_search").fadeIn(\'slow\');
	jQuery(".neno_search").css({\'position\': \'absolute\', \'zIndex\': \'5000\', \'left\':tempX-30,\'top\':tempY-10});
	/* alert(tempX+" "+tempY) */
  }

}
jQuery(document).ready(function($)
{
	jQuery("body").append(\'<img src="'.PLUGIN_URL.'images/search_icons/'.get_option('searchicon').'" class="neno_search"/>\');
	
	jQuery("body").append(\'<div id="live-search" class="live-search"><div class="result_search" id="result_search"></div><div class="searchheader"><div id="nbrposts"></div><button id="moreresult" class="more">Load more results</button></div><div id="searchedposts" class="searchedposts"></div></div>\');
	
	jQuery("body").append(\'<form name="Show"><input type="hidden" name="selectionexists" id="selectionexists"/><input type="hidden" name="selectionval" id="selectionval"/><input type="hidden" name="searchindex" id="searchindex" value="0"/></form>\');
	
	function get_search_results() {
		var postsnbr = 0;
		var nbrpagesval = 1;
	    var resultnumber = '.get_option('result_number').';
		var search_query = jQuery("#selectionval").val();
		var searchindexval= jQuery("#searchindex").val();
		
		
		if(search_query != "" && search_query.length > '.(int) get_option('min_char').' ) {
			jQuery(\'#searchedposts\').css("opacity","0.4");
			jQuery(\'#searchedposts\').css("filter","alpha(opacity=40)");
			jQuery(\'#moreresult\').text(\'Loading ...\');
				
			jQuery.ajax({
			type: "POST",
			url: "'.PLUGIN_URL.'/includes/search_results.php",
			data: "s="+search_query+"&m="+resultnumber+"&searchindexval="+searchindexval+"&postID="+'.$post->ID.',
			dataType: "xml",
			//contentType: "text/xml; charset=\"utf-8\"",
			error:function (xhr, ajaxOptions, thrownError) {
							alert("Error : "+ thrownError);
			}, 
			success: function(xml) {
				$(\'#searchedposts\').animate({scrollTop:0}, \'fast\');
				jQuery(xml).find(\'searchresponse\').each(function(){
				
				  nbrpagesval = jQuery(this).find(\'nbrpagesval\').text()
				  postsnbr = jQuery(this).find(\'postsnbr\').text()
				  if(postsnbr!=0) {
				  var posts_text = jQuery(this).find(\'posts\').text()
				  jQuery(\'#searchedposts\').prepend(posts_text);
				  jQuery(\'#searchedposts\').removeHighlight();
				  jQuery(\'#searchedposts\').highlight(search_query);
				  jQuery(\'div.postpage:first\').fadeIn("slow");
				  jQuery(\'#nbrposts\').html(\'Number of results: \'+ postsnbr);
				  } else {
				  $(\'#moreresult\').attr(\'disabled\', \'disabled\');
				  $(\'#moreresult\').css(\'color\', \'#9B9B9B\');
				  $(\'#moreresult\').css(\'background\', \'#EBEBEB\');
				  jQuery(\'#nbrposts\').html(\'\')
				  jQuery(\'#searchedposts\').html(\'There are no results\')}
				})
				
				jQuery(\'#moreresult\').text(\'Load more results\');
				jQuery(\'#searchedposts\').css("opacity","1");
				jQuery(\'#searchedposts\').css("filter","alpha(opacity=100)");
				var searchindexval2 = parseInt(searchindexval)+1;
				if(searchindexval2 == nbrpagesval) {
					$(\'#moreresult\').attr(\'disabled\', \'disabled\');
					$(\'#moreresult\').css(\'color\', \'#9B9B9B\');
					$(\'#moreresult\').css(\'background\', \'#EBEBEB\');
				}
			}
									  
		 });
									  
		}
		else
		{
				jQuery(\'#searchedposts\').html(\'Search term empty or too short.\')
		}
	
	
	}
	
	function write_results_to_page(data,status, xhr) {
	
		if (status == "error") {
			var msg = "Sorry but there was an error: ";
			console.error(msg + xhr.status + " " + xhr.statusText);
		}
		else
		{
			jQuery(\'#result_content\').html(data);
		}
		
	}
  
  $(document).bind("mouseup", function(event){
  var st = sc.Selector.getSelected();
  var selectionval = st.toString();
  jQuery("#selectionexists").val(selectionval.length);
  jQuery("#selectionval").val(selectionval);
  
  if(selectionval.length!=0 && event.target.className!= \'neno_search\'){
	jQuery(".neno_search").fadeIn(\'slow\');
	jQuery(".neno_search").css({\'position\': \'absolute\', \'zIndex\': \'5000\', \'left\':event.pageX-30,\'top\':event.pageY-10});
	//alert(tempX+" "+tempY)
  }

});

  $(".neno_search").bind("mousedown", function(){
		$(\'#moreresult\').css(\'color\', \'#000000\');
		$(\'#moreresult\').css(\'background\', \'-moz-linear-gradient(center top , #FFFFFF, #EFEFEF) repeat scroll 0 0 #F6F6F6\');
		  get_search_results();
		$("#live-search").fadeIn(\'slow\');
		$("#live-search").css({\'position\': \'absolute\', \'left\':tempX-30,\'top\':tempY+20});
		var searchedselectiontitle = ($("#selectionval").val().length < 56) ? $("#selectionval").val() :  $("#selectionval").val().substring(0, 55)+ \'...\';
		$("#result_search").html(\'<b>Search: </b>\'+ searchedselectiontitle);
		
  });
  
			$(document).mousedown(function(event) {
			$("#result").html( event.target.className);

if (($(event.target).attr("class") != "neno_search") && ($(event.target).parent().parent().attr("class") != "result_content") && ($(event.target).parent().attr("class")  != "result_content") && ($(event.target).parent().attr("class") != "live-search") && ($(event.target).parent().attr("class") != "searchheader") && ($(event.target).parent().attr("class") != "searchedposts") && ($(event.target).attr("class") != "readmore") && ($(event.target).attr("class") != "postresult") && ($(event.target).parent().attr("class") != "postresult"))
{
						 $(".neno_search").hide();
						 $("#live-search").hide();
						 $("#searchedposts").empty();
						 $("#searchindex").val(0);
						 //$(\'#moreresult\').attr(\'disabled\', \'\');
						 $(\'#moreresult\').removeAttr("disabled")

					}
			});
				
				
				document.onkeydown = function(evt) {
					evt = evt || window.event;
					if (evt.keyCode == 27) {
						$(".neno_search").hide();
						 $("#live-search").hide();
						 $("#result_content").empty();
						  $("#result_content").hide();
					}
				}; 
				
				$("#moreresult").click(function() {
				var searchindexval = $("#searchindex").val();
				searchindexval++;
					$("#searchindex").val(searchindexval)
					get_search_results();
				
				})
				
});
     </script>
	 ';
}

add_action('wp_head','Maneno_Head');

?>