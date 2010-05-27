function conformitychecker($string){
	$s = findkanjiwithouttooltip($string);
	return $string if(empty($s));
	return '<div class="note">' . $s . '</div>' . $string;
}

function findkanjiwithouttooltip($string){
//	Ranges: hira 3040-309F   kata 30A0-30FF   kanji 4E00-9FC2

//	Total kanji or kanji compounds
	preg_match_all("/([\x{4E00}-\x{9FC2}]+)/u", $string, $total);

//	Kanji or kanji compounds inside a tooltip tag
	preg_match_all("/\\\\tt\[.*([\x{4E00}-\x{9FC2}]+).*\]/uU", $string, $withtooltip);

//	Add all kanji to hash
	foreach($total[1] as $k){ $withouttooltip[$k] = 1; }

//	Remove those with tooltips
	foreach($withtooltip[1] as $k){ unset($withouttooltip[$k]); }

	$count = sizeof($withouttooltip);

//	Return if there are none without tooltips
	if($count == 0) return "";

//	Create info string
	$s = "There are " . $count . " kanji or kanji compounds without a tooltip: ";
	$s .= join(array_keys($withouttooltip), ', ');

	return $s;
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
