<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE);

define('DR' , __DIR__);
define('DP' , str_replace($_SERVER['DOCUMENT_ROOT'],"", DR));
define('UPLOAD' , DR);

$i 							= $_GET['i'];
$links 						= array();

function thumb($p = array()) {

	if (isset($p) && isset($p['thumb'])) {

		$thumb 				= $p['thumb'];
		$basename				= pathinfo(UPLOAD.$thumb, PATHINFO_BASENAME);
		$extension				= pathinfo(UPLOAD.$thumb, PATHINFO_EXTENSION);

		if (extension_loaded('imagick')) {

			header("Content-Type: image/".$extension);

			$image 			= new Imagick(UPLOAD.$thumb);
			$image->thumbnailImage(400, 0);
			echo $image;

			exit ($image);

		} else {

			list($width, $height, $type, $attr) = getimagesize(UPLOAD.$thumb);
			if ($width < 601) {

				header("Content-Type: ".$attr);
				echo file_get_contents(UPLOAD.$thumb);
				exit;

			}

		}

	}

}

if ($thumb = $_GET['thumb']) {
	thumb(array("thumb" => $thumb)); exit;
}

if ($download = $_GET['download']) {
	
	$file = urldecode($download);

	if(file_exists(DR.$download)) {
		
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.basename(DR.$download).'"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize(DR.$download));
		flush();
		readfile(DR.$download);
		exit;
	}

}

if (file_exists(DR."/config.ini")):

	$config			= parse_ini_file(DR."/config.ini");
	
endif;

if (!(isset($config))): $config = array(); endif;
if (empty($config['file'])): $config['file'] = "index.php"; endif;
if (empty($config['name'])): $config['name'] = "A BrandPack For You"; endif;
if (empty($config['font_stylesheet'])): $config['font_stylesheet'] = "https://fonts.googleapis.com/css?family=Kodchasan:400,700"; endif;
if (empty($config['font_body'])): $config['font_body'] = "'Kodchasan', sans-serif"; endif;
if (empty($config['number_of_columns'])): $config['number_of_columns'] = "5"; endif;
if (empty($config['border_radius'])): $config['border_radius'] = "1rem"; endif;
if (empty($config['text_rgb'])): $config['text_rgb'] = "68,68,68"; endif;
if (empty($config['text_hover_rgb'])): $config['text_hover_rgb'] = "255,255,255"; endif;
if (empty($config['secondary_rgb'])): $config['secondary_rgb'] = "170,170,170"; endif;
if (empty($config['tertiary_rgb'])): $config['tertiary_rgb'] = "238,238,238"; endif;

function eCount($dir) {

	$cdir = getContents(array("count" => true , "type" => "dir" , "dir" => $dir));
	$cfile = getContents(array("count" => true , "type" => "file" , "dir" => $dir));

	$return = '';
	$return .= '<div class="count">';
	if ($cdir): $return .= '<i class="dir"></i> <span>'.$cdir.'</span> '; endif;
	if ($cfile): $return .= '<i class="file"></i> <span>'.$cfile.'</span> ';  endif;
	$return .= '</div>';

	echo $return;

}

function getContents($p = array()) {

	if (true == $p['count']):

		$return 			= 0;

	else:

		$return 			= array();

	endif;

	$dir 				= "";
	
	if (!(isset($p['type']))) {

		$p['type']		= "";

	}

	if (isset($p) && isset($p['dir'])) {

		$dir 			.= $p['dir'];

	}

	$contents 			= preg_grep('/^([^.])/', scandir(UPLOAD.$dir));
	$contents			= array_diff($contents, ["index.php", "config.ini"]);

	foreach ($contents as $c):

		switch ($p['type']):

			case "dir":

				if (is_dir(UPLOAD.$dir."/".$c)):

					if (true == $p['count']):

						$return++;
	
					else:

						$return[] = $c;

					endif;

				endif;

				break;

			case "file":

				if (is_file(UPLOAD.$dir."/".$c)):

					if (true == $p['count']):

						$return++;

					else:

						$return[] = array(
							"basename" 	=> $c ,
							"filepath" 	=> $dir."/".$c ,
							"fullpath" 	=> UPLOAD.$dir."/".$c ,
							"filename" => pathinfo($dir."/".$c, PATHINFO_FILENAME) ,
							"extension" => pathinfo($dir."/".$c, PATHINFO_EXTENSION)
						);

					endif;
				
				endif;

			break;

		endswitch;

	endforeach;

	return $return;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
<title><?php echo $config['name']; ?></title>
<?php if ($config['font_stylesheet']): ?>
<link href="<?php echo $config['font_stylesheet']; ?>" rel="stylesheet">
<?php endif; ?>
<style type="text/css">
*, *::before, *::after {
    box-sizing: border-box;
}

html {
	min-height: 100%;
	overflow-y: scroll;
	font-size: 20px;
}

body {
	background-color: #fff;
	margin: 0;
	font-family: <?php echo $config['font_body']; ?>;
	color: rgb(<?php echo $config['text_rgb']; ?>);
	font-size: 1rem;
	overflow-x: hidden;
}

a {
	color: inherit;
	cursor: pointer;
	text-decoration: none;
}

ul {
	list-style-type: none;
	padding: 0;
	margin: 0;
}

header {
	position: fixed;
	z-index: 99;
	top: 0;
	left: 0;
	width: 100%;
	height: 4rem;
	background-color: rgb(<?php echo $config['secondary_rgb']; ?>);
}

header nav {
	height: 4rem;
}

header nav a {
	padding-left: 2rem;
	padding-right: 2rem;
	color: inherit;
}

header nav > ul {
	display: flex;
	height: inherit;
}

header nav > ul > li {
	position: relative;
	z-index: 1;
	height: inherit;
}

header nav > ul > li > a {
	position: relative;
	z-index: 2;
	font-weight: bold;
	height: inherit;
	display: flex;
	align-items: center;
}

header nav > ul > li:hover {
	z-index: 2;
}

header nav > ul > li:hover > a {
	color:  rgb(<?php echo $config['text_hover_rgb']; ?>);
}

header nav > ul ul {
	display: none;
	white-space: nowrap;
	min-width: 100%;
	position: absolute;
	top: 0;
	left: 0;
	padding-top: 4rem;
	padding-bottom: 2rem;
	background-color: rgb(<?php echo $config['secondary_rgb']; ?>);
	border-radius: 0 0 <?php echo $config['border_radius']; ?> <?php echo $config['border_radius']; ?>;
	box-shadow: 0 0 1rem 0 rgba(0,0,0,0.25);

}

header nav > ul > li:hover ul {
	display: block;
}

header nav > ul ul li {
	display: flex;
	justify-content: space-between;
}

header nav > ul ul li .count {
	font-size: .75rem;
	margin-right: 2rem;
}

header nav > ul ul li:hover a {
	color:  rgb(<?php echo $config['text_hover_rgb']; ?>);;
}

header .crumb {
	height: 2rem;
	background-color: rgb(<?php echo $config['tertiary_rgb']; ?>);
	padding-left: 2rem;
	padding-right: 2rem;
	display: flex;
	align-items: center;
	font-size: .75rem;
}

header .crumb ul {
	display: flex;
}

header .crumb ul li {
}

header .crumb ul li:before {
	margin-left: .5rem;
	margin-right: .5rem;
	content: '→';
}

header .crumb ul li:first-child:before {
	margin: 0;
	content: '';
}

main {
	padding-top: 8rem;
	min-height: calc(100vh - 4rem);
}

main .empty {
	display: flex;
	height: 100%;
	align-items: center;
	justify-content: center;
	text-align: center;
	font-size: 2rem;
	color: rgb(<?php echo $config['tertiary_rgb']; ?>);
}

main .dirs ,
main .files {
	display: flex;
	flex-wrap: wrap;
	margin-right: .5rem;
	margin-left: .5rem;
}

main .col {
	position: relative;
	width: 100%;
	min-height: 1px;
	padding-right: 1rem;
	padding-left: 1rem;
	flex: 0 0 100%;
    	max-width: 100%;
}

@media (min-width: 576px) {

	main .col {

		flex: 0 0 50%;
    		max-width: 50%;

	}

}

@media (min-width: 992px) {

	main .col {

		flex: 0 0 <?php echo (100 / $config['number_of_columns']); ?>%;
    		max-width: <?php echo (100 / $config['number_of_columns']); ?>%;

	}

}

main a.dir ,
main div.file {
	display: block;
	position: relative;
	overflow: hidden;
	padding-top: 100%;
	border: solid 2px;
	margin-bottom: 2rem;
	border-radius: <?php echo $config['border_radius']; ?>;
	transition: all .15s ease-in;
}

main a.dir:hover ,
main div.file:hover {
	box-shadow: 0 0 1rem 0 rgba(0,0,0,0.25);
}

main a.dir {
	background-color: rgb(<?php echo $config['secondary_rgb']; ?>);
	border-color: rgb(<?php echo $config['secondary_rgb']; ?>);
}

main div.file {
	border-color:  rgb(<?php echo $config['tertiary_rgb']; ?>);
}

main a.dir .preview ,
main div.file .preview {
	position: absolute;
	top: 0;
	left: 0;
	z-index: 1;
	width: 100%;
}

main a.dir .preview  {
	padding-top: 100%;
}

main div.file .preview {
	padding-top: 75%;
}

main a.dir .preview .name ,
main div.file .preview .coolor , 
main div.file .preview .thumbnail , 
main div.file .preview .extension {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
}

main div.file .preview .coolor {
	display: flex;
}

main div.file .preview .coolor > div {
	width: 20%;
	height: 100%;
}

main div.file .preview .thumbnail {
	background-size: contain;
	background-repeat: no-repeat;
	background-position: center;
}

main a.dir .preview .name ,
main div.file .preview .extension {
	display: flex;
	text-align: center;
	align-items: center;
	justify-content: center;
}

main a.dir .preview .name {
	font-size: 2rem;
	color: #fff;
}

main div.file .preview .extension {
	font-size: 3rem;
	color: rgb(<?php echo $config['tertiary_rgb']; ?>);
}

main a.dir .info ,
main div.file .info {
	position: absolute;
	z-index: 1;
	top: 75%;
	left: 0;
	width: 100%;
	height: 25%;
	font-size: .75rem;
	display: flex;
	align-items: center;
	justify-content: center;
	text-align: center;
}

main div.file .info {
	background-color:  rgb(<?php echo $config['tertiary_rgb']; ?>);
}

main div.file .download {
	position: absolute;
	z-index: 2;
	top: 100%;
	left: 0;
	width: 100%;
	height: 100%;
	background-color:  rgb(<?php echo $config['tertiary_rgb']; ?>);
	transition: all .15s ease-in-out;
}

main div.file .download ul {
	font-size: .75rem;
	padding: 1rem;
}

main div.file .download ul li {
	padding-bottom: .25rem;
}

main div.file .download ul li a {
	display: block;
	line-height: 1rem;
}

main div.links ul li a small ,
main div.file .download ul li a small {
	font-size: 75%;
	line-height: 125%;
	float: right;
	border-radius: .2rem;
	padding: .1333px;
	padding-left: .2rem;
	padding-right: .2rem;
	color: #fff;
	background-color: rgba(0,0,0,.5);
}

main div.file .download ul li a small {
	text-transform: uppercase;
}

main div.file:hover .download {
	top: 0;
}

main div.links {
	border-top: dotted 2px;
	border-color: rgb(<?php echo $config['secondary_rgb']; ?>);
	padding-top: 2rem;
	padding-left: 2rem;
	padding-right: 2rem;
}

main div.links ul li {
	line-height: 2rem;
}

main div.links ul li > a {
}

footer {
	color: #fff;
	padding: 2rem;
	font-size: .75rem;
	background-color:  rgb(<?php echo $config['secondary_rgb']; ?>);
}

.count {
	color: #fff;
}

.count span {
	margin-left: 4px;
	margin-right: 4px;
}

i.dir ,
i.file {
	display: inline-block;
	width: .5rem;
	height: .5rem;
	position: relative;
	overflow: hidden;
}

i.dir:before ,
i.dir:after {

	display: block;
	content: '';
	position: absolute;
	background-color: #fff;
}

i.dir:before {
	top: 0;
	width: 40%;
	height: 20%;
	border-radius: 2px 2px 0 0;
}

i.dir:after {
	top: 20%;
	width: 100%;
	height: 80%;
	border-radius: 0 2px 2px 2px;
}

i.file {
	background-color: #fff;
	border-radius: 2px;
}

i.file:after {
	display: block;
	content: '';
	position: absolute;
	width: 12px;
	top: 0px;
	right: -2px;
	border: solid 1px rgb(<?php echo $config['secondary_rgb']; ?>);
	transform: rotate(45deg)
}

</style>
</head>
<body>

<header>

	<nav>
	<?php
		
	/*
	* We scan the directory in which brandpack lives
	* and retrieve the first set of folders with which to build the topnavigation.
	*/ 

	$toplevels			= getContents(array("type" => "dir"));
	if ($toplevels):
	?>
		<ul>
		<li><a href="<?php echo $config['file']; ?>">Home</a>
		<?php foreach ($toplevels as $toplevel): ?>
		<li><a href="<?php echo $config['file']; ?>?i=/<?php echo $toplevel; ?>"><?php echo $toplevel; ?></a>
		<?php
		$sublevels			= getContents(array("type" => "dir" , "dir" => "/".$toplevel));
		if ($sublevels):
		?>
		<ul>
		<?php foreach ($sublevels as $sublevel): ?>
		<li><a href="<?php echo $config['file']; ?>?i=/<?php echo $toplevel; ?>/<?php echo $sublevel; ?>"><?php echo $sublevel; ?></a><?php eCount("/".$toplevel."/".$sublevel); ?>
		<?php
		endforeach;
		?>
		</ul>
		<?php
		endif;
		?>
		</li>
		<?php
		endforeach;
		?>
		</ul>
	<?php
	endif;
	?>
	</nav>

	<div class="crumb">

		<?php
		$prevpath = '';
		$paths = explode("/" , $i);
		if ($paths):
		?>

		<ul>
		<?php	
		foreach ($paths as $path): if ($path):
		?>

			<li><a href="<?php echo $config['file']; ?>?i=/<?php echo $prevpath; ?><?php echo $path; ?>"><?php echo $path; ?></a></li>

		<?php
		$prevpath .= $path."/";
		endif; endforeach;
		?>
		</ul>
		<?php
		endif;
		?>

	</div>

</header>

<main>

<?php
$dirs			= getContents(array("type" => "dir" , "dir" => $i));
$files		= getContents(array("type" => "file" , "dir" => $i));
?>

<?php
if (empty($dirs || $files)):
?>

<div class="empty">
This folder is empty.
</div>

<?php
endif;
?>

<?php
if ($dirs):
?>

<div class="dirs">

	<?php
	foreach ($dirs as $d):
	?>

	<div class="col">

		<a class="dir" href="<?php echo $config['file']; ?>?i=<?php if ($i): echo $i; endif; ?>/<?php echo $d; ?>">

			<div class="preview">

				<div class="name"><?php echo $d; ?></div>

			</div>

			<div class="info">

				<?php eCount($i."/".$d); ?>

			</div>

		</a>

	</div>

	<?php
	endforeach;
	?>

</div>

<?php
endif;
?>

<?php
if ($files):
?>

<div class="files">

	<?php
	foreach ($files as $f):
	
		/*
		 * Attempt to find out if this a coolors generated palette;
		 * If so, print it out
		 */

		$coolor = false;

		if (strtolower($f['extension']) == "scss"):

			$scss = file_get_contents($f['fullpath']);
			if (strpos($scss , 'color')):

				$is_coolor = true;	

			endif;

		endif;

		/*
		 * Scan any potential links
		 */

		if (strtolower($f['extension']) == "uri"):

			$links[] = $f;
			unset($f);

		endif;
	
		if ($f):
		?>

		<div class="col">

			<div class="file">

				<div class="preview">

					<?php
					if (@is_array(getimagesize($f['fullpath']))):
					?>
					
						<div class="thumbnail" style="background-image: url(<?php echo $config['file']; ?>?thumb=<?php echo $f['filepath']; ?>)"></div>
						
					<?php
					elseif (true == $is_coolor):
					?>

						<div class="coolor">

							<?php
							$coolors = explode(";" , $scss);
							$coolors = array_slice($coolors, 5); 

							foreach ($coolors as $coolor):
								$color = strstr($coolor , 'rgba');
								if ($color):
								?>

								<div style="background-color: <?php echo $color; ?>"></div>

								<?php
								endif;
							endforeach;
							?>

						</div>

					<?php
					else:
					?>
					
						<div class="extension">
								
							<?php echo $f['extension']; ?>
								
						</div>
					
					<?php
					endif;
					?>

				</div>

				<div class="info">

					<?php echo $f['filename']; ?>

				</div>

				<div class="download">

					<ul>
					<li><a href="<?php echo $config['file']; ?>?download=<?php echo $f['filepath']; ?>" target="_blank"><?php echo $f['filename']; ?> <small><?php echo $f['extension']; ?></small></a></li>
					</ul>

				</div>

			</div>

		</div>

		<?php
		endif;
	endforeach;
	?>

</div>

<?php
endif;
?>

<?php
if ($links):
?>

<div class="links">

	<ul>
	<?php
	foreach ($links as $l):

		$link = file_get_contents($l['fullpath']);
	?>

	<li><a target="_blank" href="<?php echo $link; ?>"><?php echo $link; ?> <small>(<?php echo $l['filename']; ?>)</small></a></li>

	<?php
	endforeach;
	?>
	</ul>

</div>

<?php
endif;
?>

</main>

<footer>
BrandPack is designed and developed by <a href="https://kristoffbertram.be/brandpack">Kristoff Bertram</a>.
</footer>

</body>
</html>