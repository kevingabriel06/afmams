<?php
function SaveToFile($file, $s)
{
	$f = fopen($file, 'w');
	if (!$f)
		die("Can't open file $file");
	fwrite($f, $s);
	fclose($f);
}

function MakeFont($fontfile, $enc = 'cp1252', $embed = true)
{
	// Generate a font definition file
	if (get_magic_quotes_runtime())
		@set_magic_quotes_runtime(0);

	include(dirname(__FILE__) . '/ttfparser.php');

	$parser = new TTFParser;
	$parser->Parse($fontfile);

	$s = '<?php' . "\n";
	$s .= '$type=\'TrueType\';' . "\n";
	$s .= '$name=\'' . $parser->name . '\';' . "\n";
	$s .= '$desc=' . var_export($parser->desc, true) . ";\n";
	$s .= '$up=' . $parser->underlinePosition . ';' . "\n";
	$s .= '$ut=' . $parser->underlineThickness . ';' . "\n";
	$s .= '$dw=' . $parser->defaultWidth . ';' . "\n";
	$s .= '$cw=' . var_export($parser->charWidths, true) . ";\n";
	$s .= '$enc=\'' . $enc . '\';' . "\n";
	$s .= '$diff=\'\';' . "\n";
	$s .= '$file=\'' . $parser->ShortFontName($fontfile) . '.z\';' . "\n";
	$s .= '$originalsize=' . $parser->originalSize . ';' . "\n";
	$s .= '?>';

	$basename = basename($fontfile, '.ttf');
	SaveToFile($basename . '.php', $s);

	if ($embed) {
		$f = fopen($fontfile, 'rb');
		if (!$f)
			die("Can't open file $fontfile");
		$z = fopen($basename . '.z', 'wb');
		if (!$z)
			die("Can't open file " . $basename . '.z');
		while (!feof($f)) {
			$data = fread($f, 65536);
			fwrite($z, $data);
		}
		fclose($f);
		fclose($z);
	}
}
