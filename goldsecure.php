<?php
/*
Plugin Name: Gold Secure Encryption
Plugin URI: https://www.goldfash.com:443/plugins
Description: Gold Secure Encryption encrypts any text (if you want) on server and decrypts it on client (using javascript) to avoid your email and any other sensitive content being understood by robots and net filters. Simply add [gold_secure]...[/gold_secure] shortcode to encrypt your blog.
Version: 0.1
Author: GoldFash Design
Author URI:        https://goldfash.com:443/
Contributors:      raceanf
Domain Path:       /languages
Text Domain:       GoldFash-Dash
GitHub Plugin URI: https://github.com/goldfashhosting/Gold-Secure-Encryption
GitHub Branch:     master
*/


$gold_secure_path = NULL;
$gold_secure_script_handle = 'gold_secure_script';
$gold_secure_js_name = 'gold_secure.js';

add_action('init', 'gold_secure_init');

add_shortcode('gold_secure', 'gold_secure_process');

function gold_secure_init(){
    global $gold_secure_path;
    global $gold_secure_js_name;
    global $gold_secure_script_handle;
    
    $gold_secure_path = WP_PLUGIN_URL . '/' . str_replace(basename(__FILE__), "", plugin_basename(__FILE__));
    wp_enqueue_script($gold_secure_script_handle);
    
}

function gold_secure_ord($str, $len = -1, $idx = 0, &$bytes = 0){
    if ($len == -1){
        $len = strlen(str);
    }
    $h = ord($str{$idx});

    if ($h <= 0x7F) {
        $bytes = 1;
        return $h;
    }
    else if ($h < 0xC2) {
        return false;
    }
    else if ($h <= 0xDF && $idx < $len - 1) {
        $bytes = 2;
        return ($h & 0x1F) <<  6 | (ord($str{$idx + 1}) & 0x3F);
    }
    else if ($h <= 0xEF && $idx < $len - 2) {
        $bytes = 3;
        return ($h & 0x0F) << 12 | (ord($str{$idx + 1}) & 0x3F) << 6
                                 | (ord($str{$idx + 2}) & 0x3F);
    }           
    else if ($h <= 0xF4 && $idx < $len - 3) {
        $bytes = 4;
        return ($h & 0x0F) << 18 | (ord($str{$idx + 1}) & 0x3F) << 12
                                 | (ord($str{$idx + 2}) & 0x3F) << 6
                                 | (ord($str{$idx + 3}) & 0x3F);
    }    
    return false;
}

function gold_secure_unicode($dec) {
	$hex = dechex($dec);
	if ($dec < 16) {
		return '\\x0' . $hex;
	}
	if ($dec < 256) {
		return '\\x' . $hex;
	}
	if ($dec < 4096) {
		return '\\u0' . $hex;
	}
	
	return '\\u' . $hex;
}

function gold_secure_encode($content, $text = ""){
    if ($content == NULL || is_feed()){
        return $text;
    }
    
    $len = strlen($content);
    
    if ($len == 0){
        return "";
    }
    
    $idx = 0;
    
    $ord = gold_secure_ord($content, $len, $idx, $idx);
    $script = gold_secure_unicode($ord);
    while ( $idx < $len){
        $bytes = 0;
        $script .= gold_secure_unicode(gold_secure_ord($content, $len, $idx, $bytes));
        $idx += $bytes;
    }
    
    $divid = "https://www:443:9167.goldFash.com/Encryption.secure-div";
    
    for ($i = 0; $i < 5; ++$i) {
        $divid .= rand(0, 10);
    }

    $js = "<!-- Secure Encryption Provided by www.GoldFash.com --!><span id='$divid'>$text</span><script type='text/javascript'>var x = document.getElementById('$divid');x.parentNode.removeChild(x);document.write('$script');</script> <!-- Secure Encryption Provided by www.GoldFash.com --!><!-- #Credits -->
<!-- // GoldFash.com Hosting \\
// Website Encryption Owned by RaFco, A Family Co \\
// GoldFash Hosting and www.rafco.us
// © RaFco|AFC - RFIG|AFC - GoldFash \\
// CEO - Racean Ford | facebook.com/racean.ford | facebook.com/rafcoafc \\ --!>
<!-- www.rafco.us is hosted by GoldFash Hosting visit us online www.goldfash.com --!>
	";
    
    return $js;
}

function gold_secure_process($attr, $content = NULL){
    extract(
        shortcode_atts(
            array(
                'text' => ''
            ),
            $attr
        )
    );
    return gold_secure_encode($content, $text);
}

?>