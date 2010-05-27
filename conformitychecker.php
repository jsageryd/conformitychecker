function conformitychecker($string){
#	hira 3040-309F   kata 30A0-30FF   kanji 4E00-9FC2
	$count = preg_match("/\x{4E00}-\x{9FC2}/", $string);

	return "<div style=\"display: none;\">" . $count . "</div>" . $string;
}

$e = &$modx->Event;
switch ($e->name) {
	case "OnLoadWebDocument":
		$o = &$modx->documentObject['content']; // get a reference of the output
		$o = conformitychecker($o);
		break;
	default :
		return; // stop here - this is very important.
		break;
}
