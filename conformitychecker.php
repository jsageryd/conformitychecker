namespace Conformitychecker_plugin;

// Prepends a note to the content if there are kanji without tooltips
function conformitychecker($string){
	$s = findkanjiwithouttooltip($string);
	if(empty($s)){ return $string; }
	return "<div class=\"note\">" . $s . "</div>\n" . $string;
}

// Returns a string explaining what kanji don't have tooltips
function findkanjiwithouttooltip($string){
//	Ranges: hira 3040-309F   kata 30A0-30FF   kanji 4E00-9FC2

//	Get all kanji
	preg_match_all("/([\x{4E00}-\x{9FC2}])/u", $string, $tk);

//	Return empty string if there are no kanji
	if(sizeof($tk[1]) == 0) { return ""; }

//	Add all kanji to hash with counter
	foreach($tk[1] as $k){ $totalkanji[$k] += 1; }

//	Get all kanji that are inside tooltip tags
	preg_match_all("/\\\\tt\[(.*[\x{4E00}-\x{9FC2}].*)\]/uU", $string, $ttw); // first get all words inside tooltips
	preg_match_all("/([\x{4E00}-\x{9FC2}])/u", join($ttw[1]), $ttk); // then get all kanji in those words

//	If there are any kanji in tooltips
	if(sizeof($ttk[1]) > 0) {
//		Add all tooltip kanji to hash with counter
		foreach($ttk[1] as $k){ $tooltipkanji[$k] += 1; }
	}
//	Compare the counts and make a hash of kanji with no tooltip
	foreach($totalkanji as $k => $v){
		$count = $v - $tooltipkanji[$k];
		if($count > 0) $notooltip[$k] = $count;
	}

//	Return empty string if all kanji are in tooltips
	if(sizeof($notooltip) == 0) { return ""; }

	// Get total number of kanji without a tooltip and merge kanji and number of occurences to items of a new array
	$notooltipkeyvalue = array();
	foreach($notooltip as $k => $v){
		$c += $v;
		array_push($notooltipkeyvalue, $k . ($v > 1 ? ' (' . $v . ')' : ''));
	}

//	Create info string
	$s = "There " . ($c == 1 ? 'is ' : 'are ') . $c . " kanji without a tooltip: ";
	$s .= join($notooltipkeyvalue, ', ');

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
