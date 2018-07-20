<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "maltroncraft@gmail.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "5480ab" );

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
    $public_modules   = false !== strpos('|captcha||ajax|', "|{$mod}|");
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
    if( !phpfmg_user_isLogin() ){
        exit;
    };

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
        
    $filelink =  base64_decode($_REQUEST['filelink']);
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . basename($filelink);

    // 2016-12-05:  to prevent *LFD/LFI* attack. patch provided by Pouya Darabi, a security researcher in cert.org
    $real_basePath = realpath(PHPFMG_SAVE_ATTACHMENTS_DIR); 
    $real_requestPath = realpath($file);
    if ($real_requestPath === false || strpos($real_requestPath, $real_basePath) !== 0) { 
        return; 
    }; 

    if( !file_exists($file) ){
        return ;
    };
    
    phpfmg_util_download( $file, $filelink );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function __construct(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }

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

    function __construct( $text = '', $len = 4 ){
        $this->phpfmgImage( $text, $len );
    }

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
			'7504' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nM3QMQ6AIAxA0TJwA7wPDO41oUtPUwZugEdg4ZQapwKOGm23nzR5KbRpBP60r/iIFoICgrpmJ0CQxmaCz10rLlrBgtrHe62NmZXPeEirbF7fWrkaRdWcuBSC7ywoNp+WoZk4mT/634N74zsA3FzNssuTTRAAAAAASUVORK5CYII=',
			'F931' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMZQxhDGVqRxQIaWFtZGx2mooqJNDo0BIRiiDU6wPSCnRQatXRp1tRVS5HdF9DAGIikDirGADIPTYwFixjYLWhiYDeHBgyC8KMixOI+AESczr0Fk8uUAAAAAElFTkSuQmCC',
			'BF0E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgNEQx2mMIYGIIkFTBFpYAhldEBWF9Aq0sDo6IgqBlTH2hAIEwM7KTRqatjSVZGhWUjuQ1MHNw+bGDY70N0SGgAUQ3PzQIUfFSEW9wEAKa/LV7bRaX8AAAAASUVORK5CYII=',
			'11A5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YAhimMIYGIImxOjAGMIQCZZDERB1YAxgdHR3Q9bI2BLo6ILlvZdaqqKWrIqOikNwHURfQIIKuNxSLWEOgA6ZYQACy+0RDWEOBYlMdBkH4URFicR8AKjHHBWhbX9cAAAAASUVORK5CYII=',
			'2147' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYAhgaHUNDkMREpjAGMLQ6NIggiQW0AlVORRVjaAXqDXRoCEB237RVUSszs1ZmIbsPaAdro0Mrsr2MDkCx0IApKG4BqWx0CEAWEwGLOTogi4WGsoaiiw1U+FERYnEfAIi1ydVRIiEtAAAAAElFTkSuQmCC',
			'48B8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpI37pjCGsIYyTHVAFgthbWVtdAgIQBJjDBFpdG0IdBBBEmOdgqIO7KRp01aGLQ1dNTULyX0BUzDNCw3FNI9hCjYxTL1Y3TxQ4Uc9iMV9AALmzP74acqDAAAAAElFTkSuQmCC',
			'1FC7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB1EQx1CHUNDkMRYHUSA4gENIkhiokAx1gYBFDFGsBhDQwCS+1ZmTQ1bCqKQ3AdV18qAqXcKpphAALoYo0OgA7KYaAjQFaGOKGIDFX5UhFjcBwAmQ8i6waanUgAAAABJRU5ErkJggg==',
			'D6BF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGUNDkMQCprC2sjY6OiCrC2gVaWRtCEQXa0BSB3ZS1NJpYUtDV4ZmIbkvoFUUq3mumOZhimFxC9TNKGIDFX5UhFjcBwD/58v7gP3VZgAAAABJRU5ErkJggg==',
			'2552' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM2QvRGAMAhGocgGuA8W9hShcQOdghTZwLhBmkzpT4Wnpd7J172D+94B7TYGf8onfkE6DcqFHaOFLBiIOCb5YMjkrzPFUPZ977eWWqe5jd5PILFJ8h3IJ8sXF6M0mCye7a0ZexbPVDGCosYf/O/FPPhtyYTL2CEqXDUAAAAASUVORK5CYII=',
			'DCBC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1lDGaYGIIkFTGFtdG10CBBBFmsVaXBtCHRgQRNjbXR0QHZf1NJpq5aGrsxCdh+aOoQY0Dx0MQw7sLgFm5sHKvyoCLG4DwDJ3M5CrtmNmAAAAABJRU5ErkJggg==',
			'4D22' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QMQ6AIAxFy8AN6n3q4F4TungETgEDN0DuIKcUt4qOmvD/9tKkLx/qIwFG6j9+eXIgsJNmDpOZiVkx4zAuYSVUzGaMFDig8iulHP7wdVN+fN0liPqHSGMZ0t2lMYbcsWQIuHe2soobYb/v+uJ3AqE3zIYKeMVLAAAAAElFTkSuQmCC',
			'92AD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WAMYQximMIY6IImJTGFtZQhldAhAEgtoFWl0dHR0EEERY2h0bQiEiYGdNG3qqqVLV0VmTUNyH6srwxRWhDoIbGUIYA1FFRNoZXRAVwd0SwNIDNktrAGioUB7Udw8UOFHRYjFfQAj8Mtz6x219gAAAABJRU5ErkJggg==',
			'EFAF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QkNEQx2mMIaGIIkFNIg0MIQyOjCgiTE6OmKIsTYEwsTATgqNmhq2dFVkaBaS+9DUIcRCsYhhU4cmFhqCKTZQ4UdFiMV9AES4y4g/qaUmAAAAAElFTkSuQmCC',
			'BB8B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUElEQVR4nGNYhQEaGAYTpIn7QgNEQxhCGUMdkMQCpoi0Mjo6OgQgi7WKNLo2BDqI4FYHdlJo1NSwVaErQ7OQ3Ee0eYTtwOnmgQo/KkIs7gMAHdrM+k3mrFEAAAAASUVORK5CYII=',
			'DCBD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgMYQ1lDGUMdkMQCprA2ujY6OgQgi7WKNLg2BDqIoImxAtWJILkvaum0VUtDV2ZNQ3IfmjqEGBbzMOzA4hZsbh6o8KMixOI+AMykzkaxQZxSAAAAAElFTkSuQmCC',
			'06FC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA6YGIImxBrC2sjYwBIggiYlMEWlkBapmQRILaBVpAIkhuy9q6bSwpaErs5DdF9Aq2oqkDqa30RVNDGSHK5od2NwCdnMDA4qbByr8qAixuA8AIBzJxD3pmTUAAAAASUVORK5CYII=',
			'DBE5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDHUMDkMQCpoi0sjYwOiCrC2gVaXTFFAOpc3VAcl/U0qlhS0NXRkUhuQ+ijqFBBMM8bGKMDihiYLcwBCC7D+Jmh6kOgyD8qAixuA8A64LM8yqnYzIAAAAASUVORK5CYII=',
			'70BC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkMZAlhDGaYGIIu2MoawNjoEiKCIsbayNgQ6sCCLTRFpdG10dEBxX9S0lamhK7OQ3cfogKIODFkbgGJA85DFRBow7QhowHQL0K2Ybh6g8KMixOI+AO59y06zcilRAAAAAElFTkSuQmCC',
			'581D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMYQximMIY6IIkFNLC2MoQwOgSgiIk0OgLFRJDEAgOA6qbAxcBOCpu2MmzVtJVZ05Dd14qiDiom0uiAJhaARUxkCkQvsltYAxhDGEMdUdw8UOFHRYjFfQCYysrkThA4SQAAAABJRU5ErkJggg==',
			'CB02' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WENEQximMEx1QBITaRVpZQhlCAhAEgtoFGl0dHR0EEEWA6pkBZFI7otaNTVsKZCMQnIfVF2jA6reRteGgFYGDDscpjBgcQummxlDQwZB+FERYnEfAFu7zRlBVo1ZAAAAAElFTkSuQmCC',
			'315F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7RAMYAlhDHUNDkMQCpjAGsDYwOqCobGXFFJsC1DsVLgZ20sqoVVFLMzNDs5DdB1TH0BCIZh52MVY0sQCgXkZHRxQxUaCLGULR3DJA4UdFiMV9AML/xu3rLKf1AAAAAElFTkSuQmCC',
			'DFFD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUklEQVR4nGNYhQEaGAYTpIn7QgNEQ11DA0MdkMQCpog0sDYwOgQgi7VCxERwi4GdFLV0atjS0JVZ05DcR4Re3GJY3BIaABZDcfNAhR8VIRb3AQA9tMwm/4otRQAAAABJRU5ErkJggg==',
			'BB91' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgNEQxhCGVqRxQKmiLQyOjpMRRFrFWl0bQgIRVfHCpRBdl9o1NSwlZlRS5HdB1LHEBLQim6eQwOmmCO6GMQtKGJQN4cGDILwoyLE4j4AUJbN+LJMdRIAAAAASUVORK5CYII=',
			'0191' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGVqRxVgDGAMYHR2mIouJTGENYG0ICEUWC2hlAInB9IKdFLV0VdTKTCCJ5D6QOoaQgFZ0vQwNqGIiUxgCGNHEWAMYQG5BEWN0YA0Fujk0YBCEHxUhFvcBAFPbyS6iMXIyAAAAAElFTkSuQmCC',
			'D598' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgNEQxlCGaY6IIkFTBFpYHR0CAhAFmsVaWBtCHQQQRULYW0IgKkDOylq6dSlKzOjpmYhuS+glaHRISQAzTygGKZ5jY7oYlNYW9HdEhrAGILu5oEKPypCLO4DAJMfziA5gdhgAAAAAElFTkSuQmCC',
			'2DE3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WANEQ1hDHUIdkMREpoi0sjYwOgQgiQW0ijS6guSQdUPFApDdN23aytTQVUuzkN0XgKIODBkdMM1jbcAUE2nAdEtoKKabByr8qAixuA8ArEjMlL6jpjUAAAAASUVORK5CYII=',
			'4880' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpI37pjCGMIQytKKIhbC2Mjo6THVAEmMMEWl0bQgICEASY50CUufoIILkvmnTVoatCl2ZNQ3JfQGo6sAwNBRkXiCKGMMUTDsYpmC6BaubByr8qAexuA8AXCPLs1ht2oEAAAAASUVORK5CYII=',
			'05A4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nM2QsRGAIAxFk4INcB8s7MMdaZgmFtkAR6BhSikDWuppfvfu5/Iu0C4j8Ke84odhYSggZJgjL8CwW+aLF1yDWkbqkxMqZPxyPWptOWfjRwr7JjGMu51x5DTe6D2aXJy6iWHANLOv/vdgbvxOdgvOT0kf2ioAAAAASUVORK5CYII=',
			'8AD0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WAMYAlhDGVqRxUSmMIawNjpMdUASC2hlbWVtCAgIQFEn0ujaEOggguS+pVHTVqauisyahuQ+NHVQ80RDMcVA6rDYgeYW1gCgGJqbByr8qAixuA8AKCDOGFRl43IAAAAASUVORK5CYII=',
			'BFE0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QgNEQ11DHVqRxQKmiDSwNjBMdUAWawWLBQRgqGN0EEFyX2jU1LCloSuzpiG5D00dknnYxLDZgeqW0ACgGJqbByr8qAixuA8ALevM9RRQXy4AAAAASUVORK5CYII=',
			'05AB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMIY6IImxBog0MIQyOgQgiYlMEWlgdHR0EEESC2gVCWFtCISpAzspaunUpUtXRYZmIbkvoJWh0RWhDiEWGohiHtAOsDoRFLewtrKi6WV0YATZi+LmgQo/KkIs7gMATrDLw15tENsAAAAASUVORK5CYII=',
			'3E1D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7RANEQxmmMIY6IIkFTBFpYAhhdAhAVtkq0sAIFBNBFgOpmwIXAztpZdTUsFXTVmZNQ3Yfqjq4ecSIBUD1IrsF5GbGUEcUNw9U+FERYnEfAFCsyfnUVnyJAAAAAElFTkSuQmCC',
			'53B5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkNYQ1hDGUMDkMQCGkRaWRsdHRhQxBgaXRsCUcQCAxhA6lwdkNwXNm1V2NLQlVFRyO5rBalzaBBBtrkVZF4AilhAK8QOZDGRKSC3OAQgu481AORmhqkOgyD8qAixuA8AaK/McKvpvgwAAAAASUVORK5CYII=',
			'2639' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGaY6IImJTGFtZW10CAhAEgtoFWlkaAh0EEHW3QrkNTrCxCBumjYtbNXUVVFhyO4LEG1laHSYiqyX0UGk0aEhoAFZjLUBLIZih0gDpltCQzHdPFDhR0WIxX0AEF7MNWRRoGkAAAAASUVORK5CYII=',
			'02BA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGVqRxVgDWFtZGx2mOiCJiUwRaXRtCAgIQBILaGVodG10dBBBcl/U0lVLl4auzJqG5D6guimsCHUwsQDWhsDQEBQ7GB2AYijqgG5pQNfL6CAa6hrKiCI2UOFHRYjFfQCv1cuY58LBZAAAAABJRU5ErkJggg==',
			'687A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA1qRxUSmsAL5AVMdkMQCWkQaHRoCAgKQxRqA6hodHUSQ3BcZtTJs1dKVWdOQ3BcCMm8KI0wdRG8r0LwAxtAQNDFHB1R1ILewNqCKgd2MJjZQ4UdFiMV9AGdKy/0wlL23AAAAAElFTkSuQmCC',
			'9F4D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WANEQx0aHUMdkMREpog0MLQ6OgQgiQW0AsWmOjqIoIsFwsXATpo2dWrYyszMrGlI7mN1FWlgbUTVywDUyxoaiCImADIPTR3YLY2obmENAIuhuHmgwo+KEIv7AMXRy8QMfuGrAAAAAElFTkSuQmCC',
			'8FB8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVElEQVR4nGNYhQEaGAYTpIn7WANEQ11DGaY6IImJTBFpYG10CAhAEgtoBYo1BDqI4FYHdtLSqKlhS0NXTc1Cch+x5hFhB9TNQDE0Nw9U+FERYnEfAKlyzV+mY8KbAAAAAElFTkSuQmCC',
			'53EC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkNYQ1hDHaYGIIkFNIi0sjYwBIigiDE0ujYwOrAgiQUGMADVMToguy9s2qqwpaErs1Dc14qiDiYGNg9ZLKAV0w6RKZhuYQ3AdPNAhR8VIRb3AQBd6sqbq6l3KQAAAABJRU5ErkJggg==',
			'407C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2Quw2AMAxEzxLZgIHMBkZKlmAKU7CByQYUYUo+lSMoQeDrnk66J2O9nOJPecfPICHJLJ5FilCR1jGKYYL23DgWrB157Nj75ZzLsJTB+8nRM2K/m9LOpGawMBFTtQHblxWVy+msqJ2/+t9zufHbAFwjypgAIy5kAAAAAElFTkSuQmCC',
			'64CA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7WAMYWhlCHVqRxUSmMExldAiY6oAkFtDCEMraIBAQgCzWwOjK2sDoIILkvsiopUuXrlqZNQ3JfSFTRFqR1EH0toqGujYwhoagiDEA1QmiqAO6pZXRIRBFDOJmRxSxgQo/KkIs7gMANovLSYPR7jUAAAAASUVORK5CYII=',
			'5DE3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDHUIdkMQCGkRaWRsYHQJQxRpdgbQIklhgAEQsAMl9YdOmrUwNXbU0C9l9rSjqUMSQzQvAIiYyBdMtrAGYbh6o8KMixOI+AM4tzVv63tfiAAAAAElFTkSuQmCC',
			'2BFF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WANEQ1hDA0NDkMREpoi0sjYwOiCrC2gVaXRFE2NoRVEHcdO0qWFLQ1eGZiG7LwDTPEYHTPNYGzDFRBow9YaGAt2M7pYBCj8qQizuAwART8i8OGURogAAAABJRU5ErkJggg==',
			'D18C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGaYGIIkFTGEMYHR0CBBBFmtlDWBtCHRgQRFjAKpzdEB2X9TSVVGrQldmIbsPTR1cDGQeNjEUO6YwYLglNIA1FN3NAxV+VIRY3AcAsPbKNfIlmpwAAAAASUVORK5CYII=',
			'C0AF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WEMYAhimMIaGIImJtDKGMIQyOiCrC2hkbWV0dEQVaxBpdG0IhImBnRS1atrK1FWRoVlI7kNThxALDcSwgxVNHcgt6GIgN6OLDVT4URFicR8A5jHKeIEuTbYAAAAASUVORK5CYII=',
			'95B6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGaY6IImJTBFpYG10CAhAEgtoBYo1BDoIoIqFsDY6OiC7b9rUqUuXhq5MzUJyH6srQ6NroyOKeQytQDGgeSJIYgKtIhhiIlNYW9HdwhrAGILu5oEKPypCLO4DAHgtzGzrAVZQAAAAAElFTkSuQmCC',
			'082D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMdkMRYA1hbGR0dHQKQxESmiDS6NgQ6iCCJBbSytjIgxMBOilq6MmzVysysaUjuA6trZUTTK9LoMAVVDGSHQwCqGNgtDowobgG5mTU0EMXNAxV+VIRY3AcAm47KGLtbdAUAAAAASUVORK5CYII=',
			'45D4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpI37poiGsoYyNAQgi4WINLA2OjQiizGCxBoCWpHFWKeIhADFpgQguW/atKlLl66KiopCcl/AFIZG14ZAB2S9oaFgsdAQFLeIAMUCUN0yhbUV6BY0McYQDDcPVPhRD2JxHwA5bM7M5yVM+gAAAABJRU5ErkJggg==',
			'5A19' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QkMYAhimMEx1QBILaGAMYQCKB6CIsbYyhjA6iCCJBQaINDpMgYuBnRQ2bdrKrGmrosKQ3dcKUscwFVkvQ6toKFCsAVksAKIOxQ6RKWAxFLewAu11DHVAcfNAhR8VIRb3AQAnwMx0uMXjcgAAAABJRU5ErkJggg==',
			'FBDD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVElEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDGUMdkMQCGkRaWRsdHQJQxRpdGwIdRNDVIcTATgqNmhq2dFVk1jQk96Gpw2cedjsw3ILp5oEKPypCLO4DABnVzcznY8H4AAAAAElFTkSuQmCC',
			'78F9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA6Y6IIu2srayNjAEBKCIiTS6NjA6iCCLTQGpg4tB3BS1Mmxp6KqoMCT3MTqAzZuKrJe1AWQeQwOymAhEDMWOgAZMtwQ0AN0MNA/FzQMUflSEWNwHAMCwyyxbFE01AAAAAElFTkSuQmCC',
			'A843' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHUIdkMRYA1hbGVodHQKQxESmiDQ6THVoEEESC2gFqgt0aAhAcl/U0pVhKzOzlmYhuQ+kjrURrg4MQ0NFGl1DA9DMA9rRiMWORlS3BLRiunmgwo+KEIv7AKYazn0SaENgAAAAAElFTkSuQmCC',
			'F337' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkNZQxhDGUNDkMQCGkRaWRsdGkRQxBiAIgHoYq0QUYT7QqNWha2aumplFpL7oOpaGTDNm4JFLIABwy2ODqhiYDejiA1U+FERYnEfALR/zggsBYB2AAAAAElFTkSuQmCC',
			'DC03' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgMYQxmmMIQ6IIkFTGFtdAhldAhAFmsVaXB0dGgQQRNjbQhoCEByX9TSaauWAsksJPehqUMRQzcPww4sbsHm5oEKPypCLO4DAND7zxa8QA0RAAAAAElFTkSuQmCC',
			'0CD7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1lDGUNDkMRYA1gbXRsdGkSQxESmiDS4NgSgiAW0ijSwAsUCkNwXtXTaqqWrolZmIbkPqq6VAVPvFAZMOwIYMNzi6IDFzShiAxV+VIRY3AcAQ3/M05D8mL0AAAAASUVORK5CYII=',
			'970C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WANEQx2mMEwNQBITmcLQ6BDKECCCJBbQytDo6OjowIIq1sraEOiA7L5pU1dNW7oqMgvZfayuDAFI6iCwldEBXUwAaBojmh0iU4CuQHMLK4iH5uaBCj8qQizuAwB9ecqdY9jGwwAAAABJRU5ErkJggg==',
			'718F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGUNDkEVbGQMYHR0dUFS2sgawNgSiik1hQFYHcVMUEIauDM1Cch+jAwOGeawNDBjmiWARC2jA1BvQwBoKdDOqWwYo/KgIsbgPAF+xxr8bv0FrAAAAAElFTkSuQmCC',
			'D3E9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgNYQ1hDHaY6IIkFTBFpZW1gCAhAFmtlaHRtYHQQQRUDqoOLgZ0UtXRV2NLQVVFhSO6DqGOYKoJhHkMDFjFUO7C4BZubByr8qAixuA8AKk3NBjdHAccAAAAASUVORK5CYII=',
			'E351' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkNYQ1hDHVqRxQIaRFpZGximoooxNLo2MISiibWyTmWA6QU7KTRqVdjSzKylyO4DqQOSrejmOWARc8UQE2lldER1H8jNQJeEBgyC8KMixOI+AFAmzStJRs+eAAAAAElFTkSuQmCC',
			'6ABB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYAlhDGUMdkMREpjCGsDY6OgQgiQW0sLayNgQ6iCCLNYg0uiLUgZ0UGTVtZWroytAsJPeFTEFRB9HbKhrqim5eK1AdmpgIFr2sAUAxNDcPVPhREWJxHwAp9M1LbBu6OgAAAABJRU5ErkJggg==',
			'D13C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYAhhDGaYGIIkFTGEMYG10CBBBFmtlDWBoCHRgQRFjCGBodHRAdl/U0lVRq6auzEJ2H5o6hBjQPGxiKHZMYcBwS2gAayi6mwcq/KgIsbgPAD8Wy25atOYMAAAAAElFTkSuQmCC',
			'4D21' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpI37poiGMIQytKKIhYi0Mjo6TEUWYwwRaXRtCAhFFmOdItLo0BAA0wt20rRp01Zmrcxaiuy+AJC6VlQ7QkOBYlPQ7AWpC8AQa2V0QBcTDWENDQgNGAzhRz2IxX0ANKPMTKQZvcUAAAAASUVORK5CYII=',
			'C521' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WENEQxlCGVqRxURaRRoYHR2mIosFNIo0sDYEhKKINYiEAEmYXrCTolZNXbpqZdZSZPcFNDA0OrSi2gEWm4Im1ijS6BCA7hbWVkYHVDHWEMYQ1tCA0IBBEH5UhFjcBwAy0sw4WrqJcgAAAABJRU5ErkJggg==',
			'B0DF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QgMYAlhDGUNDkMQCpjCGsDY6OiCrC2hlbWVtCEQVmyLS6IoQAzspNGraytRVkaFZSO5DUwc1D5sYNjsw3QJ1M4rYQIUfFSEW9wEAtG3Lsc5ysEoAAAAASUVORK5CYII=',
			'086F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUNDkMRYA1hbGR0dHZDViUwRaXRtQBULaGVtZQWagOy+qKUrw5ZOXRmaheQ+sDpHdL0g8wKx2IEqhs0tUDejiA1U+FERYnEfAFrGyRFAyEisAAAAAElFTkSuQmCC',
			'D501' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgNEQxmmMLQiiwVMEWlgCGWYiiLWKtLA6OgQiiYWwgokkd0XtXTq0qVAEtl9QBWNrgh1eMREGh0dHdDcwtoKdAuKWGgAYwjQzaEBgyD8qAixuA8AOzXN6SAhbFIAAAAASUVORK5CYII=',
			'1E58' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDHaY6IImxOog0sDYwBAQgiYmCxRiBJLJeoNhUuDqwk1ZmTQ1bmpk1NQvJfRBdASjmQcQCMc3DIsbo6IDqlhDRUIZQBhQ3D1T4URFicR8A+ELI3GLon9EAAAAASUVORK5CYII=',
			'279E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WANEQx1CGUMDkMREpjA0Ojo6OiCrC2hlaHRtCEQRY2hlaGVFiEHcNG3VtJWZkaFZyO4LAMIQVL2MDoxAPqoYKxAyoomJACEjmltCQ0UaGNDcPFDhR0WIxX0AsazJP2G0I7EAAAAASUVORK5CYII=',
			'EEEE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAARUlEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDHUMDkMQCGkQaWBsYHRhIEwM7KTRqatjS0JWhWUjuI9M8nGLY3DxQ4UdFiMV9AI7ayhMtI1HZAAAAAElFTkSuQmCC',
			'92DC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGaYGIImJTGFtZW10CBBBEgtoFWl0bQh0YEERYwCLIbtv2tRVS5euisxCdh+rK8MUVoQ6CGxlCEAXE2hldGBFswPolgZ0t7AGiIa6orl5oMKPihCL+wCb4cuao9N1GwAAAABJRU5ErkJggg==',
			'7D21' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGVpRRFtFWhkdHaaiiTW6NgSEoohNEWl0aAiA6YW4KWrayqyVWUuR3cfoAFTXimoHawNQbAqqmAhILABVLKAB6BYHdDHRENbQgNCAQRB+VIRY3AcALxnMRlaGK/UAAAAASUVORK5CYII=',
			'177B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB1EQ11DA0MdkMRYHRgaHRoCHQKQxEShYiIoehlaGRodYerATlqZtWraqqUrQ7OQ3AdUF8AwhRHFPEYHkCgjmnmsYHFUMZEGkCiKW0LAYihuHqjwoyLE4j4A87jIdgUrm+YAAAAASUVORK5CYII=',
			'DBC7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgNEQxhCHUNDkMQCpoi0MjoENIggi7WKNLo2CKCLtbKCaCT3RS2dGrZ01aqVWUjug6prZcAwj2EKpphAAAOGWwIdsLgZRWygwo+KEIv7AHWDzcPaaAgKAAAAAElFTkSuQmCC',
			'E43B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkMYWhlDGUMdkMQCGhimsjY6OgSgioUyNAQ6iKCIMboyINSBnRQatXTpqqkrQ7OQ3BfQINLKgGGeKNBOdPMYWjHtYGhFdws2Nw9U+FERYnEfADOYzQ3U0NLPAAAAAElFTkSuQmCC',
			'FD0F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7QkNFQximMIaGIIkFNIi0MoQyOjCgijU6OjpiiLk2BMLEwE4KjZq2MnVVZGgWkvvQ1OEVw2IHFreA3YwiNlDhR0WIxX0AuJvLvubde5EAAAAASUVORK5CYII=',
			'D162' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGaY6IIkFTGEMYHR0CAhAFmtlDWBtcHQQQRFjAIoxNIgguS9qKRBNBdJI7gOrc3RodMDQCyQxxaagiE1hALsF1c2soQyhjKEhgyD8qAixuA8AlPfLq7PIP8kAAAAASUVORK5CYII=',
			'0B18' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB1EQximMEx1QBJjDRBpZQhhCAhAEhOZItLoGMLoIIIkFtAKVDcFrg7spKilU8NWTVs1NQvJfWjqYGKNDlNQzQPZgS4GdguaXpCbGUMdUNw8UOFHRYjFfQBoe8uuYpI9PwAAAABJRU5ErkJggg==',
			'957C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA6YGIImJTBEBkgEBIkhiAa0gXqADC6pYCEOjowOy+6ZNnbp01dKVWcjuY3VlaHSYwuiAYnMrUCwAVUygVQRoGiOKHSJTWFtZGxhQ3MIawBgCFENx80CFHxUhFvcBACCNyw2ddiK1AAAAAElFTkSuQmCC',
			'F1FE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAT0lEQVR4nGNYhQEaGAYTpIn7QkMZAlhDA0MDkMQCGhgDWBsYHRhQxFixiDEgi4GdFBq1Kmpp6MrQLCT3oamjghhrKFAMxc0DFX5UhFjcBwBY68hPWjwMywAAAABJRU5ErkJggg==',
			'EDD3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAT0lEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDGUIdkMQCGkRaWRsdHQJQxRpdQSQWsQAk94VGTVuZuipqaRaS+9DUETRPhIBbsLl5oMKPihCL+wDYJs/xgEdiywAAAABJRU5ErkJggg==',
			'A433' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nM2QsQ2AMAwEP0U2CPuYgt6NG0ZgiqTwBmQEN5kSQWUllCDwd6eXfTLaMBl/yit+gaBBIORYZNRYZmLH0g5B5pwcYw0LCmV2fquZtdpsc36sSV3visgkNOyDjjegvcvJeuev/vdgbvwO+FXN9ogSYg0AAAAASUVORK5CYII=',
			'76AD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkMZQximMIY6IIu2srYyhDI6BKCIiTQyOjo6iCCLTRFpYG0IhIlB3BQ1LWzpqsisaUjuY3QQbUVSB4asDSKNrqGoYiIgMTR1AQ2sYL0BKGKMIUAxVDcPUPhREWJxHwDbTcuFrJPtLwAAAABJRU5ErkJggg==',
			'03BD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDGUMdkMRYA0RaWRsdHQKQxESmMDS6NgQ6iCCJBbQygNWJILkvaumqsKWhK7OmIbkPTR1MDMM8bHZgcws2Nw9U+FERYnEfABS6y1u0z6zWAAAAAElFTkSuQmCC',
			'45E4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpI37poiGsoY6NAQgi4WINLA2MDQiizFCxFqRxViniIQAxaYEILlv2rSpS5eGroqKQnJfwBSGRtcGRgdkvaGhYLHQEBS3iADFGFDdMoW1lRVDjDEEw80DFX7Ug1jcBwBCU801YVaVbQAAAABJRU5ErkJggg==',
			'334A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7RANYQxgaHVqRxQKmiLQytDpMdUBW2QpUNdUhIABZbApQNNDRQQTJfSujVoWtzMzMmobsPqA61ka4Orh5rqGBoSHodqCpA7sFTQziZjTzBij8qAixuA8AXMjML8Ap4YEAAAAASUVORK5CYII=',
			'CC53' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WEMYQ1lDHUIdkMREWlkbXRsYHQKQxAIaRRpcQXLIYkAe61QQjXBf1Kppq5ZmZi3NQnJfAFhXQEMAml4YiWoHqhjILY6OjihuAbmZAQiR3TxQ4UdFiMV9AELVzdpNUszTAAAAAElFTkSuQmCC',
			'1C03' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB0YQxmmMIQ6IImxOrA2OoQyOgQgiYk6iDQ4Ojo0iKDoFWlgbQhoCEBy38qsaauWropamoXkPjR1KGLo5mHagcUtIZhuHqjwoyLE4j4A+HHKeCRAvvcAAAAASUVORK5CYII=',
			'38F5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7RAMYQ1hDA0MDkMQCprC2sjYwOqCobBVpdEUXg6hzdUBy38qolWFLQ1dGRSG7D6yOoUEEwzxsYowOyGIQtzAEILsP7OYGhqkOgyD8qAixuA8AIMDKsEXqd/oAAAAASUVORK5CYII=',
			'005B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YAlhDHUMdkMRYAxhDWIEyAUhiIlNYW0FiIkhiAa0ija5T4erATopaOm1lamZmaBaS+0DqHBoCUcyDiYlg2IEqBnILo6Mjil6QmxlCGVHcPFDhR0WIxX0A0DzKUOb++dsAAAAASUVORK5CYII=',
			'60D7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYAlhDGUNDkMREpjCGsDY6NIggiQW0sLayNgSgijWINLoCyQAk90VGTVuZuipqZRaS+0KmgNW1Itsb0AoWm4IqBrYjgAHDLY4OWNyMIjZQ4UdFiMV9AB/hzLwxRWBwAAAAAElFTkSuQmCC',
			'915D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHUMdkMREpjAGsDYwOgQgiQW0soLFRFDEgHqnwsXATpo2dVXU0szMrGlI7mN1ZQhgaAhE0cvQiikmADIPTUxkCkMAo6MjiluALgllCGVEcfNAhR8VIRb3AQB6mMhaxKu7/QAAAABJRU5ErkJggg==',
			'E1B4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkMYAlhDGRoCkMQCGhgDWBsdGlHFWANYGwJaUcUYQOqmBCC5LzRqVdTS0FVRUUjug6hzdMDQ2xAYGoIhFtCAxQ4UsdAQ1lB0Nw9U+FERYnEfAM+tzYiHdXtqAAAAAElFTkSuQmCC',
			'DFF6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QgNEQ11DA6Y6IIkFTBFpYG1gCAhAFmsFiTE6CGARQ3Zf1NKpYUtDV6ZmIbkPqg6reSKExLC4JTQALIbi5oEKPypCLO4DAPqZzMUmoTVHAAAAAElFTkSuQmCC',
			'653A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WANEQxlDGVqRxUSmiDSwNjpMdUASC2gRAZEBAchiDSIhDI2ODiJI7ouMmrp01dSVWdOQ3BcyhaHRAaEOorcVKNYQGBqCIiYCEkNRJzKFtZUVTS9rAGMIYygjithAhR8VIRb3AQBLcMz3qys3igAAAABJRU5ErkJggg==',
			'DDA5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgNEQximMIYGIIkFTBFpZQhldEBWF9Aq0ujo6Igh5toQ6OqA5L6opdNWpq6KjIpCch9EXUCDCLreUCxiDYEOImhuYW0ICEB2H8jNQLGpDoMg/KgIsbgPABy2zuvh0MwHAAAAAElFTkSuQmCC',
			'28BA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGVqRxUSmsLayNjpMdUASC2gVaXRtCAgIQNbdClLn6CCC7L5pK8OWhq7MmobsvgAUdWDI6AAyLzA0BNktDWAxFHUiDZh6Q0NBbmZEERuo8KMixOI+ANECy8GQyeIxAAAAAElFTkSuQmCC',
			'AB56' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDHaY6IImxBoi0sjYwBAQgiYlMEWl0BaoWQBILaAWqm8rogOy+qKVTw5ZmZqZmIbkPpI6hIRDFvNBQkUaHhkAHEVTzgHZgiLUyOjqg6A1oFQ1hCGVAcfNAhR8VIRb3AQA8p8yS6HbwrQAAAABJRU5ErkJggg==',
			'88A5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYQximMIYGIImJTGFtZQhldEBWF9Aq0ujo6IgiBlLH2hDo6oDkvqVRK8OWroqMikJyH0RdQIMImnmuoVjEGgIdRDDsCAhAdh/IzUCxqQ6DIPyoCLG4DwArIcydcdVWlQAAAABJRU5ErkJggg==',
			'9163' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUIdkMREpjAGMDo6OgQgiQW0sgawNjg0iKCIMQDFgDSS+6ZNXRW1dOqqpVlI7mN1BapzdGhANo8BrDcAxTwBLGIiUxgw3AJ0SSi6mwcq/KgIsbgPAKy+yiVsZzuiAAAAAElFTkSuQmCC',
			'0D59' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDHaY6IImxBoi0sjYwBAQgiYlMEWl0BaoWQRILaAWKTYWLgZ0UtXTaytTMrKgwJPeB1Dk0BExF1wsUaxDBsCMAxQ6QWxgdHVDcAnIzQygDipsHKvyoCLG4DwA2gcxI+n6OzAAAAABJRU5ErkJggg=='        
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