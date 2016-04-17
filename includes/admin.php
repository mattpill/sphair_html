<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "matthew.pill88@gmail.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "864b8f" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'F0A2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMZAhimMEx1QBILaGAMYQCKB6CIsbYyOjo6iKCIiTS6gkgk94VGTVuZuioKCBHug6prdEDXGxrQyoBmB2tDwBQGNLcAxQJQxRgCWBsCQ0MGQfhREWJxHwBRvc4d16rxxwAAAABJRU5ErkJggg==',
			'0E0B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMIY6IImxBog0MIQyOgQgiYlMEWlgdHR0EEESC2gVaWBtCISpAzspaunUsKWrIkOzkNyHpg5FTISAHdjcgs3NAxV+VIRY3AcAtz3KO9jLiwAAAAAASUVORK5CYII=',
			'711F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QkMZAhimMIaGIIu2MgYwhDA6oKhsZQ1gRBebAtYLE4O4KQoIp60MzUJyH1AFsjowZG3AFBPBIhaAVYw1lDHUEdUtAxR+VIRY3AcAB1fGhgT97kEAAAAASUVORK5CYII=',
			'9731' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WANEQx1DGVqRxUSmMDS6NjpMRRYLaGVodGgICEUTA4nC9IKdNG3qqmmrpq5aiuw+VleGACR1ENjK6AA2AUlMoJW1AV1MZIpIAyuaXtYAkQbGUIbQgEEQflSEWNwHABDVzLyMs+ZwAAAAAElFTkSuQmCC',
			'2D93' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQxhCGUIdkMREpoi0Mjo6OgQgiQW0ijS6NgQ0iCDrhooFILtv2rSVmZlRS7OQ3Rcg0ugQAlcHhowOQDE081gbRBod0cREGjDdEhqK6eaBCj8qQizuAwBZ5c0ijr5G8QAAAABJRU5ErkJggg==',
			'051D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMIY6IImxBog0MIQwOgQgiYlMEWlgBIqJIIkFtIqEAPXCxMBOilo6demqaSuzpiG5L6CVodFhCrpeTDGgHRhirAGsrSA7kN3C6AB0SagjipsHKvyoCLG4DwCfc8pGu/dIWQAAAABJRU5ErkJggg==',
			'CE57' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WENEQ1lDHUNDkMREWkUaWEE0klhAIxYxII91KohGuC9q1dSwpZlZK7OQ3BcA1hXQyoCmF0hOYcCwIyAAWQzkFkZHRwd0NzOEMqKIDVT4URFicR8A3frLsgM8jncAAAAASUVORK5CYII=',
			'128A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGVqRxVgdWFsZHR2mOiCJiTqINLo2BAQEoOhlaHR0dHQQQXLfyqxVS1eFrsyahuQ+oLopjAh1MLEA1obA0BBUtzgAxdDUsTag6xUNEQ11CGVEERuo8KMixOI+AFGByCUbffYEAAAAAElFTkSuQmCC',
			'6B08' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WANEQximMEx1QBITmSLSyhDKEBCAJBbQItLo6OjoIIIs1iDSytoQAFMHdlJk1NSwpauipmYhuS9kCoo6iN5WkUbXhkBU81ox7cDmFmxuHqjwoyLE4j4AFzLM9O4qj7IAAAAASUVORK5CYII=',
			'0A2C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeUlEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGaYGIImxBjCGMDo6BIggiYlMYW1lbQh0YEESC2gVaXQAiiG7L2rptJVZKzOzkN0HVtfK6MCAolc01GEKqpjIFKC6AEYUO1gDRBodgW5Edgujg0ija2gAipsHKvyoCLG4DwAg3srF2ELUPgAAAABJRU5ErkJggg==',
			'3111' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7RAMYAhimMLQiiwVMYQxgCGGYiqKylTWAMYQhFEVsCopesJNWRq2KWjVt1VIU903BtAPII0osAIte0QDWUMZQh9CAQRB+VIRY3AcAJUTJGELMOMoAAAAASUVORK5CYII=',
			'00E0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7GB0YAlhDHVqRxVgDGENYGximOiCJiUxhbQWKBQQgiQW0ijS6Ak0QQXJf1NJpK1NDV2ZNQ3IfmjqcYtjswOYWbG4eqPCjIsTiPgAclMqOmljRUQAAAABJRU5ErkJggg==',
			'C65E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDHUMDkMREWllbWRsYHZDVBTSKNGKINYg0sE6Fi4GdFLVqWtjSzMzQLCT3BTSItjI0BKLrbXRAFwPa4YomBnILo6MjihjIzQyhjChuHqjwoyLE4j4AmlzKL8Vxe5cAAAAASUVORK5CYII=',
			'986B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMdkMREprC2Mjo6OgQgiQW0ijS6Njg6iKCIsbayNjDC1IGdNG3qyrClU1eGZiG5j9UVqA7NPAaweYEo5glgEcPmFmxuHqjwoyLE4j4AxDPLB6hlhnMAAAAASUVORK5CYII=',
			'7EA4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNFQxmmMDQEIIu2ijQwhDI0oosxOjq0oohNEWlgbQiYEoDsvqipYUtXRUVFIbmP0QGkLtABWS9rA1AsNDA0BElMpAFsHopbArCKiYaiiw1U+FERYnEfAP5lzfyJsRQiAAAAAElFTkSuQmCC',
			'81F7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA0NDkMREpjAGsIJoJLGAVlYMMZEpDGCxACT3LY1aFbU0dNXKLCT3QdW1MqCYBxabgkUsgAHDDkYHVDezhqKLDVT4URFicR8AmcrJDT+hp3sAAAAASUVORK5CYII=',
			'077A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB1EQ11DA1qRxVgDGBodGgKmOiCJiUwBiwUEIIkBdbUyNDo6iCC5L2rpqmmrlq7MmobkPqC6AIYpjDB1UDFGB4YAxtAQFDtYge5BVccaINIAEkUWA/HQxQYq/KgIsbgPAFEAyvOGlgy6AAAAAElFTkSuQmCC',
			'0F23' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGUIdkMRYA0QaGB0dHQKQxESmiDSwNgQ0iCCJBbSCeAENAUjui1o6NWzVyqylWUjuA6trZWgIQNc7hQHFPJAdDAGoYmC3ODCiuIXRAeiW0AAUNw9U+FERYnEfALh/y77hqEdBAAAAAElFTkSuQmCC',
			'3B84' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7RANEQxhCGRoCkMQCpoi0Mjo6NCKLMbSKNLo2BLSiiEHUTQlAct/KqKlhq0JXRUUhuw+sztEB07zA0BBMO7C5BUUMm5sHKvyoCLG4DwAZj83KpPamIQAAAABJRU5ErkJggg==',
			'E6BC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDGaYGIIkFNLC2sjY6BIigiIk0sjYEOrCgijWwNjo6ILsvNGpa2NLQlVnI7gtoEG1FUgc3zxVoHjYxVDsw3YLNzQMVflSEWNwHAAvZzPTc9EMmAAAAAElFTkSuQmCC',
			'71BA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMZAlhDGVpRRFsZA1gbHaY6oIixBrA2BAQEIItNAeptdHQQQXZf1KqopaErs6YhuY/RAUUdGLI2AMUaAkNDkMREIGIo6gIaMPUGNLCGsoYyoogNVPhREWJxHwAf/MmwD4HgVQAAAABJRU5ErkJggg==',
			'A99B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMdkMRYA1hbGR0dHQKQxESmiDS6NgQ6iCCJBbRCxAKQ3Be1dOnSzMzI0Cwk9wW0MgY6hASimBcaytDogGEeS6MjhhimW4DmYbh5oMKPihCL+wC+/8wbIFvTOgAAAABJRU5ErkJggg==',
			'B81C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QgMYQximMEwNQBILmMLayhDCECCCLNYq0ugYwujAgq5uCqMDsvtCo1aGrZq2MgvZfWjq4OY54BDDtAPVLSA3M4Y6oLh5oMKPihCL+wAXsMw2ZrQurwAAAABJRU5ErkJggg==',
			'3238' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7RAMYQxhDGaY6IIkFTGFtZW10CAhAVtkq0ujQEOgggiw2haHRAaEO7KSVUauWrpq6amoWsvumgFWimccQwIBuXiujA7oY0C0N6G4RDRANdURz80CFHxUhFvcBACikzOqEUKNaAAAAAElFTkSuQmCC',
			'BCEA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgMYQ1lDHVqRxQKmsDa6NjBMdUAWaxVpAIoFBKCoE2lgbWB0EEFyX2jUtFVLQ1dmTUNyH5o6uHlAsdAQDDvQ1IHdgioGcbMjithAhR8VIRb3AQBk/cz24xBOfQAAAABJRU5ErkJggg==',
			'696C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaYGIImJTGFtZXR0CBBBEgtoEWl0bXB0YEEWawCJMToguy8yaunS1Kkrs5DdFzKFMdDV0dEB2d6AVgag3kA0MRawGLId2NyCzc0DFX5UhFjcBwCPcMu2K8kIKQAAAABJRU5ErkJggg==',
			'4B85' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpI37poiGMIQyhgYgi4WItDI6Ojogq2MMEWl0bQhEEWOdAlbn6oDkvmnTpoatCl0ZFYXkvgCwOocGESS9oaEg8wJQxBimQOxAEwPpDUBxH9jNDFMdBkP4UQ9icR8AxvnLZLZDP+wAAAAASUVORK5CYII=',
			'F2FF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA0NDkMQCGlhbWRsYHRhQxEQaXTHEGJDFwE4KjVq1dGnoytAsJPcB1U3BNI8hAFOM0QFTjLUBU0w0FN0tAxV+VIRY3AcA2OzKIhoNk30AAAAASUVORK5CYII=',
			'5E5D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDHUMdkMQCGkQaWBsYHQKwiIkgiQUGAMWmwsXATgqbNjVsaWZm1jRk97WCVASi6MUmFtAKsgNVTGSKSAOjoyOKW1gDREMZQhlR3DxQ4UdFiMV9AI2LysCNz9NJAAAAAElFTkSuQmCC',
			'61BE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WAMYAlhDGUMDkMREpjAGsDY6OiCrC2hhDWBtCEQVa2BAVgd2UmTUqqiloStDs5DcFzKFAdO8VgZM87CIiWDRC3RJKLqbByr8qAixuA8A8CPI0vKHy/gAAAAASUVORK5CYII=',
			'6D91' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WANEQxhCGVqRxUSmiLQyOjpMRRYLaBFpdG0ICEURawCLwfSCnRQZNW1lZmbUUmT3hUwRaXQICUCxI6AVKNaAKeaIJgZ1C4oY1M2hAYMg/KgIsbgPAJADzV8QfYgkAAAAAElFTkSuQmCC',
			'06FB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA0MdkMRYA1hbWYEyAUhiIlNEGkFiIkhiAa0iDUjqwE6KWjotbGnoytAsJPcFtIpimAfU2+iKZh7IDnQxbG4Bu7mBEcXNAxV+VIRY3AcAaNnJ/xxu+64AAAAASUVORK5CYII=',
			'EBF3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDA0IdkMQCGkRaWRsYHQJQxRpdgbQIhjoQjXBfaNTUsKWhq5ZmIbkPTR0+83DYgeoWsJsbGFDcPFDhR0WIxX0AMz/NyXUCYSUAAAAASUVORK5CYII=',
			'B3AC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgNYQximMEwNQBILmCLSyhDKECCCLNbK0Ojo6OjAgqKOoZW1IdAB2X2hUavClq6KzEJ2H5o6uHmuoVjEgOpQ7RAB6g1AcQvIzUAxFDcPVPhREWJxHwBb6c1djr75wQAAAABJRU5ErkJggg==',
			'80DC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7WAMYAlhDGaYGIImJTGEMYW10CBBBEgtoZW1lbQh0YEFRJ9LoChRDdt/SqGkrU1dFZiG7D00d1DxsYtjswHQLNjcPVPhREWJxHwDZf8vzVpBg8AAAAABJRU5ErkJggg==',
			'D5B8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDGaY6IIkFTBFpYG10CAhAFmsFijUEOoigioUgqQM7KWrp1KVLQ1dNzUJyX0ArQ6MrhnlAMUzzMMWmsLaiuyU0gDEE3c0DFX5UhFjcBwCvcM8GSfkT7AAAAABJRU5ErkJggg==',
			'425C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM3QsRGDMAyF4ac7tAHsoxTunTtrCGcKucgGgg1omBJKmaQMR6Tub/SdsH2M4Z/2Gp9TYZU5x1b4zYY8hkZlbMlIhtDY0dJMEn3Lsq1rra/oyw6HPSXeVUU+t8MifLSha2z0kM4Cn1QUvfmu//1uv/h2LnDKo+2A/qoAAAAASUVORK5CYII=',
			'A411' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB0YWhmmADGSGGsAw1SGECBGEhOZwhDKGMIQiiwW0MroiqQX7KSopUuXrpq2aimy+wJaRTDsCA0VDXVAEwtoxXQLLjHGUIfQgEEQflSEWNwHABncy8lVtGTJAAAAAElFTkSuQmCC',
			'2D4B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WANEQxgaHUMdkMREpoi0MrQ6OgQgiQW0ijQ6THV0EEHWDRILhKuDuGnatJWZmZmhWcjuCxBpdG1ENY/RASgWGohiHmsD0LxGVDtEGoBuQdMbGorp5oEKPypCLO4DAMZhzKoK1bgvAAAAAElFTkSuQmCC',
			'82FC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA6YGIImJTGFtZW1gCBBBEgtoFWl0bWB0YEFRxwAWQ3bf0qhVS5eGrsxCdh9Q3RRWhDqoeQwBmGKMDqwYdrA2oLuFNUA01LWBAcXNAxV+VIRY3AcAR7XKkwGjSEkAAAAASUVORK5CYII=',
			'558B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNEQxlCGUMdkMQCGkQaGB0dHQLQxFgbAh1EkMQCA0RCkNSBnRQ2berSVaErQ7OQ3dfK0OiIZh5IzBXNvIBWEQwxkSmsrehuYQ1gDEF380CFHxUhFvcBAFO3y26rV+xgAAAAAElFTkSuQmCC',
			'FB62' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGaY6IIkFNIi0Mjo6BASgijW6Njg6iKCpYwXSIkjuC42aGrZ06qpVUUjuA6tzdGh0wDAvoJUBU2wKAxa3oIqB3MwYGjIIwo+KEIv7AG9nzgZNI+66AAAAAElFTkSuQmCC',
			'EFE0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVElEQVR4nGNYhQEaGAYTpIn7QkNEQ11DHVqRxQIaRBpYGximOmCKBQRgiDE6iCC5LzRqatjS0JVZ05Dch6aOgBg2O1DdEhoCFENz80CFHxUhFvcBAL8dzJ52MOwBAAAAAElFTkSuQmCC',
			'E43E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMYWhlDGUMDkMSA7KmsjY4ODKhioQwNgWhijK4MCHVgJ4VGLV26aurK0Cwk9wU0iLQyYJgnGuqAYR5DK6YdDK3obsHm5oEKPypCLO4DAIICy7G6XFwIAAAAAElFTkSuQmCC',
			'A255' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHUMDkMRYA1hbWYEyyOpEpog0uqKJBbQyNLpOZXR1QHJf1NJVS5dmZkZFIbkPqG4KkGwQQdIbGsoQgC4W0MrowNoQ6IAqBnSJo0NAAIqYaKhDKMNUh0EQflSEWNwHACuSy8iQp/mYAAAAAElFTkSuQmCC',
			'4BE0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpI37poiGsIY6tKKIhYi0sjYwTHVAEmMMEWl0bWAICEASY50CUsfoIILkvmnTpoYtDV2ZNQ3JfQGo6sAwNBRkHqoYwxRMOximYLoFq5sHKvyoB7G4DwBVtcukMOwo1gAAAABJRU5ErkJggg==',
			'6EFB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA0MdkMREpog0sDYwOgQgiQW0QMREkMUaUNSBnRQZNTVsaejK0Cwk94VgM68Vi3lYxLC5BezmBkYUNw9U+FERYnEfAEDOypxdNwyaAAAAAElFTkSuQmCC',
			'A9EB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHUMdkMRYA1hbWYEyAUhiIlNEGl2BYiJIYgGtELEAJPdFLV26NDV0ZWgWkvsCWhkDXdHMCw1lwGIeCxYxTLcAzcNw80CFHxUhFvcBABFxy40HlJngAAAAAElFTkSuQmCC',
			'14AD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YWhmmMIY6IImxOjBMZQhldAhAEhN1AIo4OjqIoOhldGVtCISJgZ20Mmvp0qWrIrOmIbmP0UGkFUkdVEw01DUUXYwBizqIGIpbQsBiKG4eqPCjIsTiPgBHtciVwKPPRQAAAABJRU5ErkJggg==',
			'E965' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGUMDkMQCGlhbGR0dHRhQxEQaXRuwiTG6OiC5LzRq6dLUqSujopDcF9DAGOjq6NAggqKXAag3AE2MBSgW6CCC4RaHAGT3QdzMMNVhEIQfFSEW9wEA1uDM5SYh8twAAAAASUVORK5CYII=',
			'33BA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7RANYQ1hDGVqRxQKmiLSyNjpMdUBW2crQ6NoQEBCALDaFAajO0UEEyX0ro1aFLQ1dmTUN2X2o6pDMCwwNwRRDUQdxC6peiJsZUc0boPCjIsTiPgAj78wDPuKdqgAAAABJRU5ErkJggg==',
			'46DF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpI37pjCGsIYyhoYgi4WwtrI2Ojogq2MMEWlkbQhEEWOdItKAJAZ20rRp08KWrooMzUJyX8AU0VZ0vaGhIo2uaGIMU7CJYboF6mZUsYEKP+pBLO4DAJ0XyjA7LDNHAAAAAElFTkSuQmCC',
			'1753' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB1EQ11DHUIdkMRYHRgaXYEyAUhiomAxhgYRFL0MraxTGRoCkNy3MmvVtKWZWUuzkNwHVBcAUhWAohcoChRBNY+1gRVDTKSB0dER1S0hQBWhDChuHqjwoyLE4j4AUGrJzxc3i6QAAAAASUVORK5CYII=',
			'4BE7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpI37poiGsIY6hoYgi4WItLICaREkMcYQkUZXNDHWKRB1AUjumzZtatjS0FUrs5DcFwBR14psb2go2LwpqG4BiwWgiQH1MjpgcTOq2ECFH/UgFvcBAD+8y1IfaIbfAAAAAElFTkSuQmCC',
			'B6F1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDA1qRxQKmsLayNjBMRRFrFWkEioWiqhNpAIrB9IKdFBo1LWxp6KqlyO4LmCLaiqQObp4rMWIQt6CIgd0MdEvAIAg/KkIs7gMA8P7M3H9Cor0AAAAASUVORK5CYII=',
			'BFD3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgNEQ11DGUIdkMQCpog0sDY6OgQgi7UCxRoCGkTQ1QHFApDcFxo1NWzpqqilWUjuQ1OH2zxcdqC5JTQAKIbm5oEKPypCLO4DABtqz1G44hJSAAAAAElFTkSuQmCC',
			'7E23' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkNFQxmA0AFZtFWkgdHR0SEATYy1IaBBBFlsCogX0BCA7L6oqWGrVmYtzUJyH6MDUF0rQwOyeawgk6YwoJgnAuIFoIqBbGR0YERxS0CDaChraACqmwco/KgIsbgPAG8py6CKfQ3AAAAAAElFTkSuQmCC',
			'F207' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMZQximMIaGIIkFNLC2MoQyNIigiIk0Ojo6oIkxNLoCyQAk94VGrVq6dFXUyiwk9wHlp7A2BLQyoOoNAIpNQRVjdGB0dAhAFWNtYAhldEAVEw11mIIqNlDhR0WIxX0AUmPM2oPYvy8AAAAASUVORK5CYII=',
			'7544' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkNFQxkaHRoCkEVbRYDYoRFDbKpDK4rYFJEQhkCHKQHI7ouaunRlZlZUFJL7GB0YGl0bHR2Q9bI2AMVCA0NDkMREGkQaHdDcEtDA2oruvoAGxhAMNw9Q+FERYnEfAD+Qzs+Jm/MrAAAAAElFTkSuQmCC',
			'3BB4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7RANEQ1hDGRoCkMQCpoi0sjY6NCKLMbSKNLo2BLSiiEHUTQlAct/KqKlhS0NXRUUhuw+sztEB07zA0BBMO7C5BUUMm5sHKvyoCLG4DwB3/M7fv+JSmAAAAABJRU5ErkJggg==',
			'ADF2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDA6Y6IImxBoi0sjYwBAQgiYlMEWl0BaoWQRILaAWJAeWQ3Be1dNrK1FAgjeQ+qLpGZDtCQ8FirQyY5k1BEwO7BVUM6OYGxtCQQRB+VIRY3AcAOLfNM7ZC3goAAAAASUVORK5CYII=',
			'2963' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUIdkMREprC2Mjo6OgQgiQW0ijS6Njg0iCDrBosB5ZDdN23p0tSpq5ZmIbsvgDHQ1dGhAdk8RgcGoN4AFPNYG1gwxEQaMN0SGorp5oEKPypCLO4DAJKvzIOqmWpYAAAAAElFTkSuQmCC',
			'F8F9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA6Y6IIkFNLC2sjYwBASgiIk0ujYwOohgqIOLgZ0UGrUybGnoqqgwJPdBzZsqgmEeQwMWMSx2oLsF6GagechuHqjwoyLE4j4Ay37Mv/LHmjEAAAAASUVORK5CYII=',
			'6F49' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WANEQx0aHaY6IImJTBFpYGh1CAhAEgtoAYpNdXQQQRZrAPIC4WJgJ0VGTQ1bmZkVFYbkvhCgeaxAO1D0tgLFQsEmoIgxNDqg2AF2SyOqW1gDwGIobh6o8KMixOI+AInszVNuJCW5AAAAAElFTkSuQmCC',
			'6CE6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WAMYQ1lDHaY6IImJTGFtdG1gCAhAEgtoEWlwbWB0EEAWaxBpYAWKIbsvMmraqqWhK1OzkNwXMgWsDtW8VoheETQxVzQxbG7B5uaBCj8qQizuAwAJQ8waCPO3+QAAAABJRU5ErkJggg==',
			'2F91' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WANEQx1CGVqRxUSmiDQwOjpMRRYLaBVpYG0ICEXRDRGD6YW4adrUsJWZUUtR3Bcg0sAQEoBiB6ODCNhUFLc0AO1FExNpALsFRSw0FKg3lCE0YBCEHxUhFvcBADZNy3CT602uAAAAAElFTkSuQmCC',
			'4FFD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpI37poiGuoYGhjogi4WINLA2MDoEIIkxQsVEkMRYp6CIgZ00bdrUsKWhK7OmIbkvYAqm3tBQTDEGLOpgYgGYYqhuHqjwox7E4j4ArarKOK6OJ/wAAAAASUVORK5CYII=',
			'8F4C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANEQx0aHaYGIImJTBFpYGh1CBBBEgtoBfKmOjqwoKsLdHRAdt/SqKlhKzMzs5DdB1LH2ghXBzePNTQQQ4yhEYsdjahuYQ0Ai6G4eaDCj4oQi/sAXv/MVZNZ3esAAAAASUVORK5CYII=',
			'2496' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYWhlCGaY6IImJTGGYyujoEBCAJBYAVMXaEOgggKy7ldEVJIbivmlLl67MjEzNQnZfgEgrQ0gginmMDqKhDkC9IshuAZmIJiYCEkNzS2goppsHKvyoCLG4DwApzsqlidR1dwAAAABJRU5ErkJggg==',
			'3AEB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7RAMYAlhDHUMdkMQCpjCGsDYwOgQgq2xlbQWJiSCLTRFpdEWoAztpZdS0lamhK0OzkN2Hqg5qnmioK7p5rRB1IihuwdQrGgAUQ3PzQIUfFSEW9wEAqbjLQC/W9yEAAAAASUVORK5CYII=',
			'F092' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGaY6IIkFNDCGMDo6BASgiLG2sjYEOoigiIk0uoJIJPeFRk1bmZkZtSoKyX0gdQ4hAY0OaHodGgJaGdDsYGwImMKAxS2oYiA3M4aGDILwoyLE4j4AgvrNP0NHXdQAAAAASUVORK5CYII=',
			'E710' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkNEQx2mMLQiiwU0MDQ6hDBMdUATcwxhCAhAFWtlmMLoIILkvtCoVdNWTVuZNQ3JfUB1AUjqoGKMDphirA0MU9DtEAGJobglNESkgTHUAcXNAxV+VIRY3AcA78rMvj7odc8AAAAASUVORK5CYII=',
			'C6B8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDGaY6IImJtLK2sjY6BAQgiQU0ijSyNgQ6iCCLNYg0IKkDOylq1bSwpaGrpmYhuS+gQRTTvAaRRld08xoxxbC5BZubByr8qAixuA8AqnDNcxAff34AAAAASUVORK5CYII=',
			'A89B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMdkMRYA1hbGR0dHQKQxESmiDS6NgQ6iCCJBbSytrICxQKQ3Be1dGXYyszI0Cwk94HUMYQEopgXGirS6IBhnkijIxY70N0S0Irp5oEKPypCLO4DAGhCy+HzBAWEAAAAAElFTkSuQmCC',
			'F0C9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkMZAhhCHaY6IIkFNDCGMDoEBASgiLG2sjYIOoigiIk0ujYwwsTATgqNmrYyddWqqDAk90HUMUzF1MvQIIJhhwCaHdjcgunmgQo/KkIs7gMA3s/M0/9Hzj4AAAAASUVORK5CYII=',
			'8D63' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WANEQxhCGUIdkMREpoi0Mjo6OgQgiQW0ijS6Njg0iKCqA4oB5ZDctzRq2srUqauWZiG5D6zO0aEB07wAFPOwiWFzCzY3D1T4URFicR8AYq/OAhRusl8AAAAASUVORK5CYII=',
			'8A85' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMDkMREpjCGMDo6OiCrC2hlbWVtCEQRE5ki0ujo6OjqgOS+pVHTVmaFroyKQnIfRJ1DgwiKeaKhrg0BaGIija5AO0Qw7HAIQHYfa4BIo0Mow1SHQRB+VIRY3AcAokvMQRIBrMwAAAAASUVORK5CYII=',
			'34DF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7RAMYWllDGUNDkMQCpjBMZW10dEBR2coQytoQiCo2hdEVSQzspJVRS5cuXRUZmoXsvikirRh6W0VDXTHEGDDUAd3Siu4WqJtR9Q5Q+FERYnEfAG+7yeoAhYKsAAAAAElFTkSuQmCC',
			'3669' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7RAMYQxhCGaY6IIkFTGFtZXR0CAhAVtkq0sja4Ogggiw2RaSBtYERJgZ20sqoaWFLp66KCkN23xTRVlZHh6kiaOa5NgQ0YBFDsQObW7C5eaDCj4oQi/sAOCzLjxQnKuMAAAAASUVORK5CYII=',
			'4DB9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpI37poiGsIYyTHVAFgsRaWVtdAgIQBJjDBFpdG0IdBBBEmOdAhRrdISJgZ00bdq0lamhq6LCkNwXAFbnMBVZb2goyLyABhEUt4DFHNDEMNyC1c0DFX7Ug1jcBwCjCc2H7CRPGgAAAABJRU5ErkJggg==',
			'CFC5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WENEQx1CHUMDkMREWkUaGB0CHZDVBTSKNLA2CKKKNYDEGF0dkNwXtWpq2NJVK6OikNwHUQc0F0MvmhjUDmQxiFsCApDdxxoCVBHqMNVhEIQfFSEW9wEAhgbLy47YOLAAAAAASUVORK5CYII=',
			'9829' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaY6IImJTGFtZXR0CAhAEgtoFWl0bQh0EEERY21lQIiBnTRt6sqwVSuzosKQ3MfqClTXyjAVWS8D0DyHKUC7kMQEQGIBDCh2gN3iwIDiFpCbWUMDUNw8UOFHRYjFfQAHa8swDYdZqwAAAABJRU5ErkJggg==',
			'8D72' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WANEQ1hDA6Y6IImJTBFpZWgICAhAEgtoFWl0aAh0EEFV1+gAFBVBct/SqGkrs5auWhWF5D6wuikMjQ7o5gUwtDKgiTk6MExhQHMLawNDAIabGxhDQwZB+FERYnEfAF+9zaimy3qaAAAAAElFTkSuQmCC',
			'388A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7RAMYQxhCGVqRxQKmsLYyOjpMdUBW2SrS6NoQEBCALAZW5+ggguS+lVErw1aFrsyahuw+VHVI5gWGhmCKoagLwKIX4mZGVPMGKPyoCLG4DwDqc8sNIu6I7AAAAABJRU5ErkJggg==',
			'6C9E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WAMYQxlCGUMDkMREprA2Ojo6OiCrC2gRaXBtCEQVaxBpYEWIgZ0UGTVt1crMyNAsJPeFTBFpYAhB09sKFEM3DyjmiCaGzS3Y3DxQ4UdFiMV9AI5LyuSTbJsPAAAAAElFTkSuQmCC',
			'EF45' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkNEQx0aHUMDkMQCGkQaGFodHRjQxaZiEQt0dHVAcl9o1NSwlZmZUVFI7gOpY210AKlG0csaGoAhxtDo6IAp5hCA7L7QELDYVIdBEH5UhFjcBwDHts2fu16AaAAAAABJRU5ErkJggg==',
			'240F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYWhmmMIaGIImJTGGYyhDK6ICsLqAVKOLoiCLG0MroytoQCBODuGna0qVLV0WGZiG7L0CkFUkdGDI6iIa6oomxgkxEs0MEKIbultBQsJtR3TJA4UdFiMV9AKBXyHXLMXNVAAAAAElFTkSuQmCC',
			'8D2A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQxhCGVqRxUSmiLQyOjpMdUASC2gVaXRtCAgIQFXX6NAQ6CCC5L6lUdNWZq3MzJqG5D6wulZGmDq4eQ5TGEND0MUCUNWB3eKAKgZyM2toIIrYQIUfFSEW9wEAWaLMGExlUnEAAAAASUVORK5CYII=',
			'3114' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7RAMYAhimMDQEIIkFTGEMYAhhaEQWY2hlDWAMYWhFEZsC1jslAMl9K6NWRa2atioqCtl9YHWMDqjmgcVCQzDE0N2CKSYawBrKGOqAIjZQ4UdFiMV9AFv/ytVCHlAJAAAAAElFTkSuQmCC',
			'EB1E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QkNEQximMIYGIIkFNIi0MoQwOjCgijU6Yoq1AvXCxMBOCo2aGrZq2srQLCT3oamDm+dAnBiGXpCbGUMdUdw8UOFHRYjFfQClHMsOjeaDmAAAAABJRU5ErkJggg==',
			'8EF7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA0NDkMREpog0sIJoJLGAVkwxmLoAJPctjZoatjR01cosJPdB1bUyYJo3BYtYAAOGHYwOGG5GExuo8KMixOI+APvHyvSA1IKxAAAAAElFTkSuQmCC',
			'0D84' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGRoCkMRYA0RaGR0dGpHFRKaINLo2BLQiiwW0ijQ6OjpMCUByX9TSaSuzQldFRSG5D6LO0QFdr2tDYGgIph3Y3IIihs3NAxV+VIRY3AcAYnXOBDsF7wgAAAAASUVORK5CYII=',
			'1233' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhDGUIdkMRYHVhbWRsdHQKQxEQdRBodGgIaRFD0MjQ6gEUR7luZtWrpqqmrlmYhuQ+obgoDQh1MLIABwzygKIYYawOGW0JEQx3R3DxQ4UdFiMV9AGruysnDWQmbAAAAAElFTkSuQmCC',
			'E09A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGVqRxQIaGEMYHR2mOqCIsbayNgQEBKCIiTS6NgQ6iCC5LzRq2srMzMisaUjuA6lzCIGrQ4g1BIaGoNnB2ICuDuQWRxQxiJsZUcQGKvyoCLG4DwBeAcwtY74ixQAAAABJRU5ErkJggg==',
			'E500' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkNEQxmmMLQiiwU0iDQwhDJMdUATY3R0CAhAFQthbQh0EEFyX2jU1KVLV0VmTUNyH1BPoytCHR4xkUZHDDtYW9HdEhrCGILu5oEKPypCLO4DAI0AzVZKAt/uAAAAAElFTkSuQmCC',
			'0730' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7GB1EQx1DGVqRxVgDGBpdGx2mOiCJiUxhaHRoCAgIQBILaAXqa3R0EEFyX9TSVdNWTV2ZNQ3JfUB1AUjqoGKMDgwNgShiIlNYQTIodrAGiDSwormFEaiLEc3NAxV+VIRY3AcArX7MeX7y1/cAAAAASUVORK5CYII=',
			'012D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGUMdkMRYAxgDGB0dHQKQxESmsAawNgQ6iCCJBbQC9SLEwE6KWroqatXKzKxpSO4Dq2tlxNQ7BVVMZApQLABVjBUswojiFkYH1lDW0EAUNw9U+FERYnEfAJtrx6y+XcQMAAAAAElFTkSuQmCC',
			'A424' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7GB0YWhlCGRoCkMRYAximMjo6NCKLiUxhCGVtCGhFFgtoZXQFklMCkNwXtXTp0lUrs6KikNwX0CrSytDK6ICsNzRUNNRhCmNoCIp5QLcEoLolAKwTU4w1NABFbKDCj4oQi/sAaUHNcnuBWR0AAAAASUVORK5CYII=',
			'834F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANYQxgaHUNDkMREpoi0MrQ6OiCrC2hlaHSYiiomMoWhlSEQLgZ20tKoVWErMzNDs5DcB1LH2ohpnmtoIKYdjeh2AN2CJgZ1M4rYQIUfFSEW9wEAj/HKzwEjiZQAAAAASUVORK5CYII=',
			'7440' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMZWhkaHVpRRFsZpjK0Okx1QBULZZjqEBCALDaF0ZUh0NFBBNl9UUuXrszMzJqG5D5GB5FW1ka4OjBkbRANdQ0NRBETaQC7BcWOAIgYilugYqhuHqDwoyLE4j4ASV7Mbw5p4yQAAAAASUVORK5CYII='        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>