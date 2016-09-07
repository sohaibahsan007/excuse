<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "sohaibahsan007@live.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "f67951" );

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

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
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
			'64DB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYWllDGUMdkMREpjBMZW10dAhAEgtoYQhlbQh0EEEWa2B0BYkFILkvMmrp0qWrIkOzkNwXMkWkFUkdRG+raKgrunmtDK3odgDd0oruFmxuHqjwoyLE4j4AKobMPnwzyzIAAAAASUVORK5CYII=',
			'22D6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGaY6IImJTGFtZW10CAhAEgtoFWl0bQh0EEDW3coAFkNx37RVS5euikzNQnZfAMMU1oZAFPMYHRgCgGIOIshuAYqii4kARdHdEhoqGuqK5uaBCj8qQizuAwDqn8v1KPb2SwAAAABJRU5ErkJggg==',
			'6112' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYAhimMEx1QBITmcIYwBDCEBCAJBbQwhrAGMLoIIIs1gDW2yCC5L7IqFVRq6atAhII94VMAatrRLYjoBUs1sqAKTaFAcUtYLEAVDezhjKGOoaGDILwoyLE4j4ASCbJ6QarJuUAAAAASUVORK5CYII=',
			'FF77' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7QkNFQ11DA0NDkMQCGkTgJEGxRgcgjXBfaNTUsFVLV63MQnIfWN0UhlYGdL0BQFE0MUYHoCiaGCtIlIDYQIUfFSEW9wEAAEXNLBUxedkAAAAASUVORK5CYII=',
			'707A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QkMZAlhDA1pRRFsZQxgaAqY6oIixAtUEBAQgi00RaXRodHQQQXZf1LSVWUtXZk1Dch+jA1DdFEaYOjBkbQCKBTCGhiCJiTSwtjI6oKoLaGAMYW1AFwO6GU1soMKPihCL+wD8qMrsYoz4CgAAAABJRU5ErkJggg==',
			'F32E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkNZQxhCGUMDkMQCGkRaGR0dHRhQxBgaXRsC0cVaGRBiYCeFRq0KW7UyMzQLyX1gda2MGOY5TMEiFoAuBnSLA7oYawhraCCKmwcq/KgIsbgPAFjZyrPRGzTgAAAAAElFTkSuQmCC',
			'4A16' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpI37pjAEAPFUB2SxEMYQhhCGgAAkMcYQ1lbGEEYHASQx1ikijQ5TGB2Q3Tdt2rSVWdNWpmYhuS8Aog7FvNBQ0VCQXhEUt0DMwxRDdQtIzDHUAdXNAxV+1INY3AcAEInLzqf12GAAAAAASUVORK5CYII=',
			'909B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMdkMREpjCGMDo6OgQgiQW0srayNgQ6iKCIiTS6AsUCkNw3beq0lZmZkaFZSO5jdRVpdAgJRDGPAajXAc08AaAdjGhi2NyCzc0DFX5UhFjcBwA82cqQf8VYvgAAAABJRU5ErkJggg==',
			'0F2D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGUMdkMRYA0QaGB0dHQKQxESmiDSwNgQ6iCCJBbSCeHAxsJOilk4NW7UyM2sakvvA6loZMfVOQRUD2cEQgCoGdosDI4pbQCpYQwNR3DxQ4UdFiMV9AIncyf086TslAAAAAElFTkSuQmCC',
			'85EE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WANEQ1lDHUMDkMREpog0sDYwOiCrC2jFFAOqC0ESAztpadTUpUtDV4ZmIblPZApDoyuGedjERDDERKawtqLbyxrAGILu5oEKPypCLO4DACGvyckrX84KAAAAAElFTkSuQmCC',
			'D213' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QgMYQximMIQ6IIkFTGFtZQhhdAhAFmsVaXQMYWgQQRFjaHSYAqSR3Be1dNXSVdNWLc1Cch9Q3RQGhDqYWABIDNU8RgcMsSmsDQxTUN0SGiAa6hjqgOLmgQo/KkIs7gMAXP7N9TS/dcIAAAAASUVORK5CYII=',
			'2A7B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA0MdkMREpjCGMDQEOgQgiQW0sraCxESQdbeKNDo0OsLUQdw0bdrKrKUrQ7OQ3RcAVDeFEcU8RgfRUIcARhTzWBtEgKahiokAxVwbUPWGhoLFUNw8UOFHRYjFfQBj1cuXFUm+fAAAAABJRU5ErkJggg==',
			'F95D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDHUMdkMQCGlhbWRsYHQJQxEQaXYFiIuhiU+FiYCeFRi1dmpqZmTUNyX0BDYyBDg2BaHoZGjHFWIB2oIuxtjI6OqK5hTGEIZQRxc0DFX5UhFjcBwC6ZcymrMfnawAAAABJRU5ErkJggg==',
			'0FF0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7GB1EQ11DA1qRxVgDRBpYGximOiCJiUwBiwUEIIkFtILEGB1EkNwXtXRq2NLQlVnTkNyHpg6nGDY7sLkFpAsohuLmgQo/KkIs7gMAnU7K4uvss90AAAAASUVORK5CYII=',
			'290A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYQximMLQii4lMYW1lCGWY6oAkFtAq0ujo6BAQgKwbKObaEOggguy+aUuXpq6KzJqG7L4AxkAkdWDI6MAA0hsaguyWBhagHY4o6kQaQG5hRBELDQW5GVVsoMKPihCL+wD9c8sKThyygAAAAABJRU5ErkJggg==',
			'8083' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUIdkMREpjCGMDo6OgQgiQW0srayNgQ0iKCoE2kEKmsIQHLf0qhpK7NCVy3NQnIfmjqoeSKNrmjmYbcD0y3Y3DxQ4UdFiMV9AH9SzIAjA6CJAAAAAElFTkSuQmCC',
			'AC70' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7GB0YQ1lDA1qRxVgDWBsdGgKmOiCJiUwRaQCKBQQgiQW0ijQwNDo6iCC5L2rptFWrlq7MmobkPrC6KYwwdWAYGgrkBaCKgdQ5OjCg2cHa6NrAgOKWgFagmxsYUNw8UOFHRYjFfQCZPs1cX8/asgAAAABJRU5ErkJggg==',
			'9D8F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7WANEQxhCGUNDkMREpoi0Mjo6OiCrC2gVaXRtCMQQc0SoAztp2tRpK7NCV4ZmIbmP1RVFHQRiMU8Aixg2t0DdjGreAIUfFSEW9wEAm43J9OD3tE0AAAAASUVORK5CYII=',
			'7AFE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkMZAlhDA0MDkEVbGUNYGxgdUFS2srZiiE0RaXRFiEHcFDVtZWroytAsJPcxOqCoA0PWBtFQdDGRBkx1AbjFUN08QOFHRYjFfQDWScnZRrphvwAAAABJRU5ErkJggg==',
			'C9F5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDA0MDkMREWllbWRsYHZDVBTSKNLqiizWAxVwdkNwXtWrp0tTQlVFRSO4LaGAMdAWZi6KXoRFDrJEFbAeyGMQtDAHI7gO7uYFhqsMgCD8qQizuAwBPr8uYm6lxNwAAAABJRU5ErkJggg==',
			'A0DC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB0YAlhDGaYGIImxBjCGsDY6BIggiYlMYW1lbQh0YEESC2gVaXQFiiG7L2rptJWpqyKzkN2Hpg4MQ0MxxQJasdmB6ZaAVkw3D1T4URFicR8A3X3MO6O2nkEAAAAASUVORK5CYII=',
			'088E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMDkMRYA1hbGR0dHZDViUwRaXRtCEQRC2hFUQd2UtTSlWGrQleGZiG5D00dVAzTPGx2YHMLNjcPVPhREWJxHwBvrMlAmQskXAAAAABJRU5ErkJggg==',
			'5C5A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QkMYQ1lDHVqRxQIaWBtdGximOqCIiTQAxQICkMQCA0QaWKcyOogguS9s2rRVSzMzs6Yhu68VpCIQpg5ZLDQE2Y5WkB2o6kSmsDY6OjqiiLEGMIYyhDKimjdA4UdFiMV9AIlOzCcfaaXlAAAAAElFTkSuQmCC',
			'06C9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhCHaY6IImxBrC2MjoEBAQgiYlMEWlkbRB0EEESC2gVaWAFmiCC5L6opdPClgKpMCT3BbSKtrI2MExF09voCjIXzQ7XBgEUO7C5BZubByr8qAixuA8AyIjLL4dmGjUAAAAASUVORK5CYII=',
			'F09A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGVqRxQIaGEMYHR2mOqCIsbayNgQEBKCIiTS6NgQ6iCC5LzRq2srMzMisaUjuA6lzCIGrQ4g1BIaGoNnB2ICuDuQWRzQxkJsZUcQGKvyoCLG4DwCDGMxVGf7r9QAAAABJRU5ErkJggg==',
			'C678' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDA6Y6IImJtLK2MjQEBAQgiQU0ijQyNAQ6iCCLNQB5jQ4wdWAnRa2aFrZq6aqpWUjuC2gQbWWYwoBqXoMIUCcjqnlAOxwdUMVAbmFtQNULdnMDA4qbByr8qAixuA8AEozMtwcRDFYAAAAASUVORK5CYII=',
			'AB17' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB1EQximMIaGIImxBoi0MoQwNIggiYlMEWl0RBMLaAWqmwKkkdwXtXRq2Kppq1ZmIbkPqq4V2d7QUJFGhykg3SjmgcQCGDDsYHRAFRMNYQx1RBEbqPCjIsTiPgAPH8w8BaT4lAAAAABJRU5ErkJggg==',
			'FC90' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkMZQxlCGVqRxQIaWBsdHR2mOqCIiTS4NgQEBKCJsTYEOogguS80atqqlZmRWdOQ3AdSxxACV4cQa8AUc8SwA5tbMN08UOFHRYjFfQBd2c37VvvUhgAAAABJRU5ErkJggg==',
			'637C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WANYQ1hDA6YGIImJTBFpZWgICBBBEgtoYWh0aAh0YEEWa2BoZWh0dEB2X2TUqrBVS1dmIbsvZApQ3RRGB2R7A4A6HQIwxRwdGFHsALmFtYEBxS1gNzcwoLh5oMKPihCL+wCcLMuEB+84gAAAAABJRU5ErkJggg==',
			'6BC6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WANEQxhCHaY6IImJTBFpZXQICAhAEgtoEWl0bRB0EEAWaxBpZW1gdEB2X2TU1LClq1amZiG5L2QKWB2qea0g8xgdRDDEBFHEsLkFm5sHKvyoCLG4DwBZvcxh9REeiQAAAABJRU5ErkJggg==',
			'E918' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMYQximMEx1QBILaGBtZQhhCAhAERNpdAxhdBBBE3OYAlcHdlJo1NKlWdNWTc1Ccl9AA2MgkjqoGANQL7p5LFjEgG5B0wtyM2OoA4qbByr8qAixuA8AgvrNT4LoQ+UAAAAASUVORK5CYII=',
			'E658' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDHaY6IIkFNLC2sjYwBASgiIk0sjYwOoigijWwToWrAzspNGpa2NLMrKlZSO4LaBBtBZIY5jk0BKKb1+iKIcbayujogKIX5GaGUAYUNw9U+FERYnEfAGs2zT2Hbrt8AAAAAElFTkSuQmCC',
			'117C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YAlhDA6YGIImxOjAGMDQEBIggiYk6sALFAh1Y0PQyNDo6ILtvZdaqqFVLgSSS+8DqpjA6oNvLEIApxujAiGEHawMDqltCWEOBYihuHqjwoyLE4j4AnvXF8IaQt6YAAAAASUVORK5CYII=',
			'3A75' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7RAMYAlhDA0MDkMQCpjCGMDQEOqCobGVtxRCbItLo0Ojo6oDkvpVR01ZmLV0ZFYXsPpC6KQwNIijmiYY6BKCLiTQ6OjA6iKC4RaTRtYEhANl9ogFgsakOgyD8qAixuA8A083MHmad7e4AAAAASUVORK5CYII=',
			'15F7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDA0NDkMRYHUQaWIG0CJKYKBYxRgeREJBYAJL7VmZNXbo0FEghuY/RgaHRtYGhFdVesNgUVDERkFgAqhhrKytINbJbQhhD0MUGKvyoCLG4DwC1r8hnbfilZQAAAABJRU5ErkJggg==',
			'59DC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDGaYGIIkFNLC2sjY6BIigiIk0ujYEOrAgiQUGQMSQ3Rc2benS1FWRWSjua2UMRFIHFWNoRBcLaGXBsENkCqZbWAMw3TxQ4UdFiMV9ANDAzIt0i27kAAAAAElFTkSuQmCC',
			'0EC0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7GB1EQxlCHVqRxVgDRIDiAVMdkMREpog0sDYIBAQgiQW0gsQYHUSQ3Be1dGrY0lUrs6YhuQ9NHU4xbHZgcws2Nw9U+FERYnEfAK+vywAbZchVAAAAAElFTkSuQmCC',
			'0A24' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGRoCkMRYAxhDGB0dGpHFRKawtrI2BLQiiwW0ijQ6NARMCUByX9TSaSuzVmZFRSG5D6yuldEBVa9oqMMUxtAQFDuA6gLQ3SLS6OiAKsboINLoGhqAIjZQ4UdFiMV9AJD2zYxT1n0AAAAAAElFTkSuQmCC',
			'204D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WAMYAhgaHUMdkMREpjCGMLQ6OgQgiQW0srYyTHV0EEHW3SrS6BAIF4O4adq0lZmZmVnTkN0XINLo2oiql9EBKBYaiCLG2gC0A02dSAPQLY2obgkNxXTzQIUfFSEW9wEA2xDLIzqhhBYAAAAASUVORK5CYII=',
			'58DF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDGUNDkMQCGlhbWRsdHRhQxEQaXRsCUcQCA4DqEGJgJ4VNWxm2dFVkaBay+1pR1EHFMM0LwCImMgXTLawBYDejmjdA4UdFiMV9AL5Dys9vokWnAAAAAElFTkSuQmCC',
			'30F3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7RAMYAlhDA0IdkMQCpjCGsDYwOgQgq2xlbWUF0iLIYlNEGl1B6pHctzJq2srU0FVLs5Ddh6oOah5ETISAHdjcAnZzAwOKmwcq/KgIsbgPAIXQy6C2mWX8AAAAAElFTkSuQmCC',
			'FABA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkMZAlhDGVqRxQIaGENYGx2mOqCIsbayNgQEBKCIiTS6Njo6iCC5LzRq2srU0JVZ05Dch6YOKiYa6toQGBqCbl5DIJo6bHqBYqGMKGIDFX5UhFjcBwCy9s5gnNvttgAAAABJRU5ErkJggg==',
			'D6EC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDHaYGIIkFTGFtZW1gCBBBFmsVaWRtYHRgQRVrAIkhuy9q6bSwpaErs5DdF9Aq2oqkDm6eKw4xFDuwuAWbmwcq/KgIsbgPAPG5zAnlF8TKAAAAAElFTkSuQmCC',
			'27BA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WANEQ11DGVqRxUSmMDS6NjpMdUASC2gFijUEBAQg625laGVtdHQQQXbftFXTloauzJqG7L4AhgAkdWDI6MDowNoQGBqC7BYwDERRJwKE6HpDQ4FioYwoYgMVflSEWNwHANzly6CNBTdfAAAAAElFTkSuQmCC',
			'79CE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMZQxhCHUMDkEVbWVsZHQIdUFS2ijS6Ngiiik0BiTHCxCBuilq6NHXVytAsJPcxOjAGIqkDQ9YGhkZ0MZEGFgw7Ahow3RLQgMXNAxR+VIRY3AcAQ9LJ6j3FnZMAAAAASUVORK5CYII=',
			'0B5C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDHaYGIImxBoi0sjYwBIggiYlMEWl0BapmQRILaAWqm8rogOy+qKVTw5ZmZmYhuw+kjqEh0IEBVW+jA5oYxI5AFDtAbmF0dEBxC8jNDKEMKG4eqPCjIsTiPgB4B8rZJ23ESgAAAABJRU5ErkJggg==',
			'B3D8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QgNYQ1hDGaY6IIkFTBFpZW10CAhAFmtlaHRtCHQQQVHH0MraEABTB3ZSaNSqsKWroqZmIbkPTR1u87DagekWbG4eqPCjIsTiPgBZ/c6+pSgHzQAAAABJRU5ErkJggg==',
			'3492' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7RAMYWhlCGaY6IIkFTGGYyujoEBCArBKoirUh0EEEWWwKoytrQ0CDCJL7VkYtXboyM2pVFLL7poi0MoQENDqgmCca6tAQ0MqAakcrI8h2VLe0gtyC6WbG0JBBEH5UhFjcBwCH8Mun/jiLswAAAABJRU5ErkJggg==',
			'762B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGUMdkEVbWVsZHR0dAlDERBpZGwIdRJDFpoB4gTB1EDdFTQtbtTIzNAvJfYwOoq0MrYwo5rE2iDQ6TGFEMU8EJBaAKhbQAHSLA6regAbGENbQQFQ3D1D4URFicR8A0LPKYoyKcYcAAAAASUVORK5CYII=',
			'B76D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QgNEQx1CGUMdkMQCpjA0Ojo6OgQgi7UyNLo2ODqIoKprZW1ghImBnRQatWra0qkrs6YhuQ+oLoDVEU1vK6MDa0MgmhhrA4bYFJEGRjS3hAYAVaC5eaDCj4oQi/sAvLbMhD6Yx+wAAAAASUVORK5CYII=',
			'170C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB1EQx2mMEwNQBJjdWBodAhlCBBBEhMFijk6OjqwoOhlaGVtCHRAdt/KrFXTlq6KzEJ2H1BdAJI6qBijA6YYawMjhh1AV6C7JQTIQ3PzQIUfFSEW9wEAGKLIBgpZhgkAAAAASUVORK5CYII=',
			'FD01' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QkNFQximMLQiiwU0iLQyhDJMRRNrdHR0CEUXc20IgOkFOyk0atrK1FVRS5Hdh6YOrxjQDmxuQRMDuzk0YBCEHxUhFvcBAK24zkrdgvxOAAAAAElFTkSuQmCC',
			'A8FB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA0MdkMRYA1hbWYEyAUhiIlNEGl2BYiJIYgGtKOrATopaujJsaejK0Cwk96GpA8PQUGzmEbQDKgZ0cwMjipsHKvyoCLG4DwDBnMtO4fgl8wAAAABJRU5ErkJggg==',
			'CEDB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7WENEQ1lDGUMdkMREWkUaWBsdHQKQxAIagWINgQ4iyGINELEAJPdFrZoatnRVZGgWkvvQ1KGIiRCwA5tbsLl5oMKPihCL+wBS1MxP+WddkgAAAABJRU5ErkJggg==',
			'8540' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WANEQxkaHVqRxUSmiDQwtDpMdUASC2gFik11CAhAVRfCEOjoIILkvqVRU5euzMzMmobkPpEpDI2ujXB1UPOAYqGBaGIijQ6N6HawAlWiuoU1gDEE3c0DFX5UhFjcBwDIRc2KEvTsWAAAAABJRU5ErkJggg==',
			'5E71' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDA1qRxQIaREDkVCxiochigQFAsUYHmF6wk8KmTQ1btRQIkd3XClQ3hQHFDrBYAKpYAFCM0QFVTGSKSANrA6oYawDQzQ0MoQGDIPyoCLG4DwBQK8v3qoUbfAAAAABJRU5ErkJggg==',
			'27A2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2QsQ2AIBBFz4INcJ9jg2/iNU4DBRugG9gwpafVT7TURF73AncvSL+dLH/ik76A0bTJquRik6ImADlUKSkljfy6Sg0ZOXLf1re9Lw71QeD3Cu8YdNBgPpVbLtDYRccd2JmdbrL5B//3Ig99B485zIIHiel0AAAAAElFTkSuQmCC',
			'BBCF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgNEQxhCHUNDkMQCpoi0MjoEOiCrC2gVaXRtEEQVA6pjbWCEiYGdFBo1NWzpqpWhWUjuQ1OHZB42MUw70N0CdTOK2ECFHxUhFvcBAFOoy2GDzbHgAAAAAElFTkSuQmCC',
			'6EA3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WANEQxmmMIQ6IImJTBFpYAhldAhAEgtoEWlgdHRoEEEWA/JYgWQAkvsio6aGLV0VtTQLyX0hU1DUQfS2AsVCA1DNa4WoE0FzC2tDIIpbQG4GqkNx80CFHxUhFvcBAJ7WzZec6ZWaAAAAAElFTkSuQmCC',
			'73DF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNZQ1hDGUNDkEVbRVpZGx0dUFS2MjS6NgSiik1haGVFiEHcFLUqbOmqyNAsJPcxOqCoA0PWBkzzRLCIBTRguiWgAexmVLcMUPhREWJxHwDGbMpE8Gc7BgAAAABJRU5ErkJggg==',
			'5B42' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM2QsQ2AMAwE3bCB2ccU9I6UULABTBEX3iBhB5gS0xlBCVL83emlPxmOx2VoKb/4pdhHEKrkGGdUUGK+M2sNhI4Ftl6gjM5v2uq0L+sxez9F7YTEbxiTMbF6FzZmreIZFtsQYs86vpyHFBv434d58TsBkLXN7iLgQWcAAAAASUVORK5CYII=',
			'D9FF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDA0NDkMQCprC2sjYwOiCrC2gVaXTFLwZ2UtTSpUtTQ1eGZiG5L6CVMRBTLwMW81gwxbC4BexmNLGBCj8qQizuAwDnm8r1aJI10gAAAABJRU5ErkJggg==',
			'66FD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA0MdkMREprC2sjYwOgQgiQW0iDSCxESQxRpEGpDEwE6KjJoWtjR0ZdY0JPeFTBFtxdDbKtLoSoQYNreA3dzAiOLmgQo/KkIs7gMARRHKsXV9dFoAAAAASUVORK5CYII=',
			'011A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB0YAhimMLQii7EGMAYwhDBMdUASE5kCFA1hCAhAEgtoBelldBBBcl/U0lVRq6atzJqG5D40dchioSEodmCqYw3AFGN0YA1lDHVEERuo8KMixOI+ACYXyBZuvdQAAAAAAElFTkSuQmCC',
			'165B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHUMdkMRYHVhbWYEyAUhiog4ijSAxERS9Ig2sU+HqwE5amTUtbGlmZmgWkvsYHURbGRoCUcwD6m10AIqhmdfoiiHG2sro6IjqlhDGEIZQRhQ3D1T4URFicR8AB/rILWMr+wsAAAAASUVORK5CYII=',
			'BAC3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgMYAhhCHUIdkMQCpjCGMDoEOgQgi7WytrI2CDSIoKgTaXQF0UjuC42atjJ11aqlWUjuQ1MHNU80FCSGYl4rSB2mHY5obgkNEGl0QHPzQIUfFSEW9wEAY9jPCebtOHAAAAAASUVORK5CYII=',
			'0033' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7GB0YAhhDGUIdkMRYAxhDWBsdHQKQxESmsLYyNAQ0iCCJBbSKNDo0OjQEILkvaum0lVlTVy3NQnIfmjqEGJp52OzA5hZsbh6o8KMixOI+AAyMzOtU8bZtAAAAAElFTkSuQmCC',
			'D1FB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgMYAlhDA0MdkMQCpjAGsDYwOgQgi7WygsVEUMQYkNWBnRS1FIhCV4ZmIbkPTR2KGDbzUMSmYOoNBboYKIbi5oEKPypCLO4DAJU4ygwKtjsAAAAAAElFTkSuQmCC',
			'8140' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYAhgaHVqRxUSmMAYwtDpMdUASC2gFqpzqEBCAog6oN9DRQQTJfUujVkWtzMzMmobkPpA61ka4Oqh5QLHQQAwxoFsw7WhEdQsrUCe6mwcq/KgIsbgPAKuTywQ6JAE8AAAAAElFTkSuQmCC',
			'7D20' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGVpRRFtFWhkdHaY6oIo1ujYEBAQgi00RaXRoCHQQQXZf1LSVWSszs6YhuY/RAaiulRGmDgxZG4BiU1DFREBiAQwodgQ0AN3iwIDiloAG0RDW0ABUNw9Q+FERYnEfADDBzDwtuZM1AAAAAElFTkSuQmCC',
			'51A7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMYAhimMIaGIIkFNDAGMIQyNIigiLEGMDo6oIgFBjAEsAJlApDcFzZtVdTSVVErs5Dd1wpW14piM0gsNGAKslgARF0AspjIFJBYoAOyGCtQJ7rYQIUfFSEW9wEAJDPKaPcUuPIAAAAASUVORK5CYII=',
			'72B2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nM2QMQ6AMAhF6cAN6n1YumNSFk8DQ29Qj+DiKW0naXTUpPyB5IXAC3A+SmGm/OInEjIK7ORpwYJGzAOLlnSl6FkFS0Yavd92Hof0dvsFgtr2mb+BCozKxbvENtlY9YzbZHcZ2SJJguQJ/vdhXvwu8sbMzp2iQ9wAAAAASUVORK5CYII=',
			'E5C0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkNEQxlCHVqRxQIaRBoYHQKmOqCJsTYIBASgioWwAlWKILkvNGrq0qWrVmZNQ3IfUE+jK0IdHjERoBi6Hayt6G4JDWEMQXfzQIUfFSEW9wEAkyrNVk0JTtYAAAAASUVORK5CYII=',
			'8992' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaY6IImJTGFtZXR0CAhAEgtoFWl0bQh0EEFRBxILaBBBct/SqKVLMzOjVkUhuU9kCmOgQ0hAowOKeQxAPpBEEWNpdGwImMKAxS2YbmYMDRkE4UdFiMV9ABhEzNw0my3oAAAAAElFTkSuQmCC',
			'FF4B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkNFQx0aHUMdkMQCGkQaGFodHQLQxaY6OoigiwXC1YGdFBo1NWxlZmZoFpL7QOpYGzHNYw0NxDSvEYsdWPQyoLl5oMKPihCL+wD0s82hH2/BtAAAAABJRU5ErkJggg==',
			'CCD3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7WEMYQ1lDGUIdkMREWlkbXRsdHQKQxAIaRRpcGwIaRJDFgDxWIBmA5L6oVdNWLV0VtTQLyX1o6lDERAjYgc0t2Nw8UOFHRYjFfQCT787qQCT4EgAAAABJRU5ErkJggg==',
			'1EA4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMDQEIImxOog0MIQyNCKLiQLFGB0dWgNQ9Io0sDYETAlAct/KrKlhS1dFRUUhuQ+iLtABQ29oYGgIpnkNWOxAERMNEQ1FFxuo8KMixOI+ALtvy1Ka3wd7AAAAAElFTkSuQmCC',
			'8031' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WAMYAhhDGVqRxUSmMIawNjpMRRYLaGUFqgkIRVUn0ujQ6ADTC3bS0qhpK7OmrlqK7D40dVDzgGJAEosd2NyCIgZ1c2jAIAg/KkIs7gMAFWTM/tvdUeYAAAAASUVORK5CYII=',
			'EB7B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDA0MdkMQCGkRaGRoCHQJQxRodgGIi6OoaHWHqwE4KjZoatmrpytAsJPeB1U1hxDQvgBHdPKBpGGKtrA2oesFubmBEcfNAhR8VIRb3AQB1lcz8PnwmIwAAAABJRU5ErkJggg==',
			'C9C4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WEMYQxhCHRoCkMREWllbGR0CGpHFAhpFGl0bBFpRxBpAYgxTApDcF7Vq6dJUIBWF5L6ABsZA1wagiSh6GYB6GUNDUOxgAdmBzS0oYtjcPFDhR0WIxX0AHcXOhrz5PB4AAAAASUVORK5CYII=',
			'041B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YWhmmMIY6IImxBjBMZQhhdAhAEhOZwhDKCBQTQRILaGV0BeqFqQM7KWrp0qWrpq0MzUJyX0CrSCuSOqiYaKjDFFTzgHaA1YmgugVDL8jNjKGOKG4eqPCjIsTiPgAkWMnkuBi4EAAAAABJRU5ErkJggg==',
			'5E2B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkNEQxlCGUMdkMQCGkQaGB0dHQLQxFgbAh1EkMQCA0C8QJg6sJPCpk0NW7UyMzQL2X2tQHWtjCjmgcWmMKKYFwASC0AVE5kCdIsDql7WANFQ1tBAFDcPVPhREWJxHwBZUsqCV2LLYgAAAABJRU5ErkJggg==',
			'AB98' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGaY6IImxBoi0Mjo6BAQgiYlMEWl0bQh0EEESC2gVaWVtCICpAzspaunUsJWZUVOzkNwHUscQEoBiXmioSKMDpnmNjljsQHdLQCummwcq/KgIsbgPAAfkzSxEjUztAAAAAElFTkSuQmCC',
			'2139' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYAhhDGaY6IImJTGEMYG10CAhAEgtoBapsCHQQQdbdyhDA0OgIE4O4adqqqFVTV0WFIbsvAKTOYSqyXkYHoFhDQAOyGGsDWAzFDiAbwy2hoayh6G4eqPCjIsTiPgBkGsoCPltp2AAAAABJRU5ErkJggg==',
			'632C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WANYQxhCGaYGIImJTBFpZXR0CBBBEgtoYWh0bQh0YEEWa2BoZQCKIbsvMmpV2KqVmVnI7guZAlTXyuiAbG9AK0OjwxQsYgGMKHaA3eLAgOIWkJtZQwNQ3DxQ4UdFiMV9AJXzyt4eirS/AAAAAElFTkSuQmCC',
			'81C5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYAhhCHUMDkMREpjAGMDoEOiCrC2hlDWBtEEQRE5nCABRjdHVAct/SqFVRS1etjIpCch9EHZBGMQ+XmKCDCJodjA4BAcjuA7oklCHUYarDIAg/KkIs7gMAVGrJSA+Nyu0AAAAASUVORK5CYII=',
			'CE4E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WENEQxkaHUMDkMREWkUaGFodHZDVBTQCxaaiiTUAxQLhYmAnRa2aGrYyMzM0C8l9IHWsjZh6WUMDMe1AUwd2C5oYNjcPVPhREWJxHwCs7Mr8QTGmUQAAAABJRU5ErkJggg==',
			'D80F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QgMYQximMIaGIIkFTGFtZQhldEBWF9Aq0ujo6IgmxtrK2hAIEwM7KWrpyrClqyJDs5Dch6YObp4rFjEMO7C4BepmFLGBCj8qQizuAwApictDgOe9AwAAAABJRU5ErkJggg==',
			'5CD4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYQ1lDGRoCkMQCGlgbXRsdGlHFRBpcGwJakcUCA0QaWBsCpgQguS9s2rRVS1dFRUUhu68VpC7QAVkvVCw0BNmOVrAdKG4RmQJ2C4oYawCmmwcq/KgIsbgPAKRxz6TJIo97AAAAAElFTkSuQmCC',
			'9885' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMDkMREprC2Mjo6OiCrC2gVaXRtCEQTA6tzdUBy37SpK8NWha6MikJyH6srSJ1DgwiyzWDzAlDEBKB2iGC4xSEA2X0QNzNMdRgE4UdFiMV9AH5MyvvHTfj8AAAAAElFTkSuQmCC',
			'1D9B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGUMdkMRYHURaGR0dHQKQxEQdRBpdGwIdRFD0QsQCkNy3MmvayszMyNAsJPeB1DmEBKKYBxbDYp4jphimW0Iw3TxQ4UdFiMV9AH1PyUkSmy8cAAAAAElFTkSuQmCC',
			'BE79' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDA6Y6IIkFTBEBkQEByGKtILFABxF0dY2OMDGwk0KjpoatWroqKgzJfWB1UximiqCbF8DQgC7G6MCAYQcrUCWyW8BubmBAcfNAhR8VIRb3AQCiE80mHE0+AwAAAABJRU5ErkJggg==',
			'A7CA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7GB1EQx1CHVqRxVgDGBodHQKmOiCJiUxhaHRtEAgIQBILaGVoZQWaIILkvqilq6YtXbUyaxqS+4DqApDUgWFoKKMDUCw0BMU81gbWBkEUdQGtIkCdgRhiDKGOKGIDFX5UhFjcBwDJpsvUdUYuuwAAAABJRU5ErkJggg==',
			'1593' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1EQxmA0AFJjNVBpIHR0dEhAElMFCjG2hDQIIKiVyQEJBaA5L6VWVOXrsyMWpqF5D5GB4ZGhxC4OoQYpnmNjhhirK0YbglhDEF380CFHxUhFvcBAGi0yhFYT9zmAAAAAElFTkSuQmCC',
			'E92C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGaYGIIkFNLC2Mjo6BIigiIk0ujYEOrCgiTkAxZDdFxq1dGnWyswsZPcFNDAGOrQyOjCg6GVodJiCLsbS6BDAiGYHK0gniltAbmYNDUBx80CFHxUhFvcBABhYzAULhR4KAAAAAElFTkSuQmCC',
			'BD0A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgNEQximMLQiiwVMEWllCGWY6oAs1irS6OjoEBCAqq7RtSHQQQTJfaFR01amrorMmobkPjR1cPOAYqEhGHY4oqoDu4URRQziZlSxgQo/KkIs7gMAZCHNyYvWoNEAAAAASUVORK5CYII=',
			'E6F4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDAxoCkMQCGlhbWRsYGlHFRBqBYq1oYg1AsSkBSO4LjZoWtjR0VVQUkvsCGkSB5jE6oJvn2sAYGoIhxoDNLShiYDejiQ1U+FERYnEfALj6zkIxmg5wAAAAAElFTkSuQmCC',
			'CD83' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WENEQxhCGUIdkMREWkVaGR0dHQKQxAIaRRpdGwIaRJDFGkQagcoaApDcF7Vq2sqs0FVLs5Dch6YOLoZhHhY7sLkFm5sHKvyoCLG4DwBn2c4ChyS9DQAAAABJRU5ErkJggg==',
			'3C43' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7RAMYQxkaHUIdkMQCprA2OrQ6OgQgq2wVaXCY6tAggiw2BcgLdGgIQHLfyqhpq1ZmZi3NQnYfUB3QxAZ081hDA1DNA9nRiGoH2C2NqG7B5uaBCj8qQizuAwDHa85F+VZKogAAAABJRU5ErkJggg==',
			'3605' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7RAMYQximMIYGIIkFTGFtZQhldEBR2SrSyOjoiCo2RaSBtSHQ1QHJfSujpoUtXRUZFYXsvimirawNAQ0iaOa5YhFzBNohguEWhgBk90HczDDVYRCEHxUhFvcBAGV9yv/F+krNAAAAAElFTkSuQmCC'        
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