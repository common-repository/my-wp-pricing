<?php
/* Plugin Name: WP Pricing 

Plugin URI: paisleyfarmersmarket.ca/sohels/
Description: This is WP Pricing  wordpress  plugin really looking awesome Pricing. Everyone can use the WP Pricing  plugin easily like other wordpress plugin. Here everyone can Pricing from post, page or other custom post. Also can use Pricing from every category. By using Pricing shortcode use the every where post, page and template.
Author: Md sohel
Version: 1.0
Author URI: paisleyfarmersmarket.ca/sohels/
*/


//////////////////////////////////////////////////////////////////

// Remove extra P tags
//////////////////////////////////////////////////////////////////
function ms_pricing_shortcodes_formatter($content) {
	$block = join("|",array("pricing_table", "pricing_column", "pricing_price", "pricing_row", "pricing_footer"));

	// opening tag
	$rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]",$content);

	// closing tag
	$rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)/","[/$2]",$rep);

	return $rep;
}

add_filter('the_content', 'ms_pricing_shortcodes_formatter');
add_filter('widget_text', 'ms_pricing_shortcodes_formatter');

///////////////////////////////////////
// Shortcode Test elemante 
///////////////////////////////////////
/*

[pricing_table type="2" backgroundcolor="" bordercolor="" dividercolor=""]
[pricing_column title="Premium"]
[pricing_price currency="$" price="19.99" time="mo"][/pricing_price]
[pricing_row]5 Projects[/pricing_row]
[pricing_row]5 GB Storage[/pricing_row]
[pricing_row]Unlimited Users[/pricing_row]
[pricing_row]10 GB Bandwith[/pricing_row]
[pricing_row]Enhanced Security[/pricing_row]
[pricing_footer]Sign Up Now![/pricing_footer]
[/pricing_column]
[pricing_column title="Premium"]
[pricing_price currency="$" price="29.99" time="mo"][/pricing_price]
[pricing_row]20 Projects[/pricing_row]
[pricing_row]40 GB Storage[/pricing_row]
[pricing_row]Unlimited Users[/pricing_row]
[pricing_row]50 GB Bandwith[/pricing_row]
[pricing_row]Enhanced Security[/pricing_row]
[pricing_footer]Sign Up Now![/pricing_footer]
[/pricing_column]
[pricing_column title="Premium"]
[pricing_price currency="$" price="39.99" time="mo"][/pricing_price]
[pricing_row]65 Projects[/pricing_row]
[pricing_row]100 GB Storage[/pricing_row]
[pricing_row]Unlimited Users[/pricing_row]
[pricing_row]150 GB Bandwith[/pricing_row]
[pricing_row]Enhanced Security[/pricing_row]
[pricing_footer]Sign Up Now![/pricing_footer]
[/pricing_column]
[/pricing_table]

*/

//////////////////////////////////////////////////////////////////
// Pricing table
//////////////////////////////////////////////////////////////////
add_shortcode('pricing_table', 'ms_shortcode_pricing_table');
	function ms_shortcode_pricing_table($atts, $content = null) {
		global $data;

		extract(shortcode_atts(array(
			'backgroundcolor' => '',
			'bordercolor' => '',
			'dividercolor' => ''
		), $atts));

		static $ms_pricing_table_counter = 1;

		if(!$backgroundcolor) {
			$backgroundcolor = $data['pricing_bg_color'];
		}

		if(!$bordercolor) {
			$bordercolor = $data['pricing_border_color'];
		}

		if(!$dividercolor) {
			$dividercolor = $data['pricing_divider_color'];
		}

		$str = "<style type='text/css'>
		#pricing-table-{$ms_pricing_table_counter}.full-boxed-pricing{background-color:{$bordercolor} !important;}
		#pricing-table-{$ms_pricing_table_counter} .column{background-color:{$backgroundcolor} !important;border-color:{$dividercolor} !important;}
		#pricing-table-{$ms_pricing_table_counter}.sep-boxed-pricing .column{background-color:{$bordercolor} !important;}
		#pricing-table-{$ms_pricing_table_counter} .column li{border-color:{$dividercolor} !important;}
		#pricing-table-{$ms_pricing_table_counter} li.normal-row{background-color:{$backgroundcolor} !important;}
		#pricing-table-{$ms_pricing_table_counter}.full-boxed-pricing li.title-row{background-color:{$backgroundcolor} !important;}
		#pricing-table-{$ms_pricing_table_counter} li.pricing-row,#pricing-table-{$ms_pricing_table_counter} li.footer-row{background-color:{$bordercolor} !important;}
		</style>";

		if($atts['type'] == '2') {
			$type = 'sep';
		} elseif($atts['type'] == '1') {
			$type = 'full';
		} else {
			$type = 'third';
		}
		$str .= '<div id="pricing-table-'.$ms_pricing_table_counter.'" class="'.$type.'-boxed-pricing">';
		$str .= do_shortcode($content);
		$str .= '</div><div class="clear"></div>';

		$ms_pricing_table_counter++;

		return $str;
	}

//////////////////////////////////////////////////////////////////
// Pricing Column
//////////////////////////////////////////////////////////////////
add_shortcode('pricing_column', 'ms_shortcode_pricing_column');
	function ms_shortcode_pricing_column($atts, $content = null) {
		$str = '<div class="column">';
		$str .= '<ul>';
		if($atts['title']):
		$str .= '<li class="title-row">'.$atts['title'].'</li>';
		endif;
		$str .= do_shortcode($content);
		$str .= '</ul>';
		$str .= '</div>';

		return $str;
	}

//////////////////////////////////////////////////////////////////
// Pricing Row
//////////////////////////////////////////////////////////////////
add_shortcode('pricing_price', 'ms_shortcode_pricing_price');
	function ms_shortcode_pricing_price($atts, $content = null) {
		$str = '';
		$str .= '<li class="pricing-row">';
		if(isset($atts['currency']) && !empty($atts['currency']) && isset($atts['price']) && !empty($atts['price'])) {
			$class = '';
			$price = explode('.', $atts['price']);
			if($price[1]){
				$class .= 'price-with-decimal';
			}
			$str .= '<div class="price '.$class.'">';
				$str .= '<strong>'.$atts['currency'].'</strong>';
				$str .= '<em class="exact_price">'.$price[0].'</em>';
				if($price[1]){
					$str .= '<sup>'.$price[1].'</sup>';
				}
				if($atts['time']) {
					$str .= '<em class="time">'.$atts['time'].'</em>';
				}
			$str .= '</div>';
		} else {
			$str .= do_shortcode($content);
		}
		$str .= '</li>';

		return $str;
	}

//////////////////////////////////////////////////////////////////
// Pricing Row
//////////////////////////////////////////////////////////////////
add_shortcode('pricing_row', 'ms_shortcode_pricing_row');
	function ms_shortcode_pricing_row($atts, $content = null) {
		$str = '';
		$str .= '<li class="normal-row">';
		$str .= do_shortcode($content);
		$str .= '</li>';

		return $str;
	}

//////////////////////////////////////////////////////////////////
// Pricing Footer
//////////////////////////////////////////////////////////////////
add_shortcode('pricing_footer', 'ms_shortcode_pricing_footer');
	function ms_shortcode_pricing_footer($atts, $content = null) {
		$str = '';
		$str .= '<li class="footer-row">';
		$str .= do_shortcode($content);
		$str .= '</li>';

		return $str;
	}


function ms_pricing_shortcode_jquery() {
	wp_enqueue_script('jquery');
}
add_action('init', 'ms_pricing_shortcode_jquery'); 

add_action('wp_footer', 'ms_pricing_script');
	function ms_pricing_script(){?>
	<script type="text/javascript">
		jQuery(document).ready(function(){
	
			
			jQuery(".single_table").hover(function() {
				
				jQuery(".single_table").removeClass("active");
				jQuery(this).addClass("active");
				
			});
			
			
			
		});

	</script>
	<?php 
	}
	
add_action('wp_head', 'ms_pricing_style_css');
	function ms_pricing_style_css(){?>
		
<style type="text/css">.plan-wrap {
  border: 4px solid #d8d8d8;
  border-radius: 4px;
  box-sizing: border-box;
  display: block;
  margin: 0 auto;
  width: 940px;
}
	.full-boxed-pricing {
    background: none repeat scroll 0 0 #f7f5f5;
    float: left;
    margin-bottom: 20px;
    overflow: hidden;
    padding: 9px;
}
.full-boxed-pricing .column {
    -moz-border-bottom-colors: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    border-color: #e5e4e3 -moz-use-text-color -moz-use-text-color #e5e4e3;
    border-image: none;
    border-style: solid none none solid;
    border-width: 1px 0 0 1px;
    float: left;
}
.full-boxed-pricing .column:last-child {
    border-right: 1px solid #e5e4e3;
}
.full-boxed-pricing ul {
    list-style: none outside none;
    margin: 0;
    padding: 0;
    width: 183px;
}
.full-boxed-pricing ul li {
    background: none repeat scroll 0 0 #ffffff;
    border-bottom: 1px solid #ededed;
    margin: 0;
    padding: 15px 0;
    text-align: center;
}
.full-boxed-pricing ul li.title-row {
    color: #333333;
    font: 18px "MuseoSlab500Regular",arial,helvetica,sans-serif !important;
}
.full-boxed-pricing ul li.pricing-row {
    background: none repeat scroll 0 0 #f8f8f8;
    color: #a0ce4e;
    font: 25px "MuseoSlab500Regular",arial,helvetica,sans-serif !important;
}
.full-boxed-pricing ul li.pricing-row span {
    color: #888888 !important;
    font-size: 11px !important;
}
.full-boxed-pricing ul li.footer-row {
    background: none repeat scroll 0 0 #f7f7f6;
}
.sep-boxed-pricing {
    margin-bottom: 20px;
    overflow: hidden;
}
.sep-boxed-pricing .column {
    background: none repeat scroll 0 0 #f7f5f5;
    float: left;
    margin-left: 15px;
    overflow: hidden;
    padding: 9px;
}
.sep-boxed-pricing .column:first-child {
    margin-left: 0;
}
.sep-boxed-pricing ul {
    list-style: none outside none;
    margin: 0;
    padding: 0;
}
.sep-boxed-pricing ul li {
    -moz-border-bottom-colors: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    background: none repeat scroll 0 0 #fff;
    border-color: #dddcdc #dddcdc -moz-use-text-color;
    border-image: none;
    border-style: solid solid none;
    border-width: 1px 1px 0;
    margin: 0;
    padding: 15px 0;
    text-align: center;
    width: 283px;
}
.sep-boxed-pricing ul li:last-child {
    border-bottom: 1px solid #dddcdc;
}
.sep-boxed-pricing ul li.title-row {
    background: none repeat scroll 0 0 #92c563;
    border-color: #92c563;
    color: #333333;
    font: 18px "MuseoSlab500Regular",arial,helvetica,sans-serif !important;
}
.sep-boxed-pricing ul li.pricing-row {
    background: none repeat scroll 0 0 #f8f8f8;
    color: #a0ce4e;
    font: 25px "MuseoSlab500Regular",arial,helvetica,sans-serif !important;
}
.sep-boxed-pricing ul li.pricing-row span {
    color: #888888 !important;
    font-size: 11px !important;
}
.sep-boxed-pricing ul li.footer-row {
    background: none repeat scroll 0 0 #f7f7f6;
}
.full-boxed-pricing .price strong {
    color: #505050;
    font-size: 21px;
    position: relative;
    top: -15px;
}
.full-boxed-pricing .price em.exact_price {
    display: inline !important;
    font-size: 55px !important;
    font-style: normal !important;
    font-weight: bold !important;
}
.full-boxed-pricing .price sup {
    font-size: 17px;
    font-weight: bold;
    position: relative;
    top: -16px;
}
.full-boxed-pricing .price em.time {
    color: #888 !important;
    font-size: 11px !important;
    margin-left: 0 !important;
    position: relative;
    top: -5px;
}
.full-boxed-pricing .price-with-decimal em.time {
    margin-left: -12px !important;
}
.sep-boxed-pricing .price strong {
    color: #505050;
    font-size: 28px;
    position: relative;
    top: -30px;
}
.sep-boxed-pricing .price em.exact_price {
    display: inline !important;
    font-size: 75px !important;
    font-style: normal !important;
    font-weight: bold !important;
}
.sep-boxed-pricing .price sup {
    font-size: 23px;
    font-weight: bold;
    position: relative;
    top: -24px;
}
.sep-boxed-pricing .price em.time {
    color: #888 !important;
    font-size: 26px !important;
    margin-left: 0 !important;
    position: relative;
    top: -2px;
}
.sep-boxed-pricing .price-with-decimal em.time {
    margin-left: -18px !important;
}
	
.pricing-row .exact_price, .pricing-row sup {
    color: #a0ce4e !important;
}



@media only screen and (max-width: 800px){

.full-boxed-pricing{
		width:97%;
	}
	.full-boxed-pricing .column{
		width:100%;
		border:1px solid #E5E4E3 !important;
		margin-bottom:10px;
	}
	.full-boxed-pricing ul{
		width:100%;
	}

	.sep-boxed-pricing .column{width:100%;box-sizing:border-box;margin-left:0;}
	.sep-boxed-pricing ul{
		width:100%;
	}
	.sep-boxed-pricing ul li{
		width:100%;
	}
}

@media only screen and (min-device-width: 320px) and (max-device-width: 640px){


	.full-boxed-pricing{
		width:97%;
	}
	.full-boxed-pricing .column{
		width:100%;
		border:1px solid #E5E4E3 !important;
		margin-bottom:10px;
	}
	.full-boxed-pricing ul{
		width:100%;
	}

	.sep-boxed-pricing .column{width:100%;box-sizing:border-box;margin-left:0;}
	.sep-boxed-pricing ul{
		width:100%;
	}
	.sep-boxed-pricing ul li{
		width:100%;
	}

}

@media only screen and (max-width: 640px){
	
	.sep-boxed-pricing .column{width:100%;box-sizing:border-box;margin-left:0;}
}


</style>
		<?php
	}