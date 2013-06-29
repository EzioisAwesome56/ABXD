<?php
$bbcode = array(
	'b' => array(
		'callback' => 'bbcodeBold',
	),
	'i' => array(
		'callback' => 'bbcodeItalic',
	),
	'u' => array(
		'callback' => 'bbcodeUnderline',
	),
	's' => array(
		'callback' => 'bbcodeStrikethrough',
	),

	'url' => array(
		'callback' => 'bbcodeURL',
		'pre' => 'bbcodeURLPRE',
	),
	'img' => array(
		'callback' => 'bbcodeImage',
		'pre' => true,
	),
	'imgs' => array(
        'callback' => 'bbcodeImageScale',
        'pre' => true,
	),

	'user' => array(
		'callback' => 'bbcodeUser',
		'void' => true,
	),

	'thread' => array(
		'callback' => 'bbcodeThread',
		'void' => true,
	),

	'forum' => array(
		'callback' => 'bbcodeForum',
		'void' => true,
	),

	'quote' => array(
		'callback' => 'bbcodeQuote',
	),
	'reply' => array(
		'callback' => 'bbcodeReply',
	),

	'spoiler' => array(
		'callback' => 'bbcodeSpoiler',
	),
);

function bbcodeAppend($dom, $nodes) {
	foreach (iterator_to_array($nodes) as $node)
		$dom->appendChild($node);
}

function domToString($dom) {
	if ($dom instanceof DOMNodeList)
	{
		$result = "";
		foreach ($dom as $elem)
			$result .= domToString($elem);
		return $result;
	}
	return $dom->ownerDocument->saveHTML($dom);
}

function markupToMarkup($dom, $markup) {
	$markup_dom = new DOMDocument;
	$markup_dom->loadHTML($markup);
	$nodes = $markup_dom->getElementsByTagName('body')->item(0)->childNodes;
	$result = array();
	foreach ($nodes as $node)
		$result[] = $dom->importNode($node, true);

	return $result;
}

function parseQuoteLike($quote, $i = 0, $full = false) {
	if ($quote[$i] === '"')
	{
		$pos = strpos($quote, '"', $i + 1);
		if ($pos)
			return array(
				'pos'    => $pos + 1,
				'substr' => substr($quote, $i + 1, $pos - $i - 1),
			);
	}
	elseif ($quote[$i] === "'")
	{
		$pos = strpos($quote, "'", $i + 1);
		if ($pos)
			return array(
				'pos'    => $pos + 1,
				'substr' => substr($quote, $i + 1, $pos - $i - 1),
			);
	}
	// If not, then it's unquoted
	if ($full)
		return array(
			'pos'    => strlen($quote),
			'substr' => substr($quote, $i),
		);
	else
	{
		$pos = strpos($quote, ' ', $i);
		if (!$pos) $pos = strlen($quote);
		return array(
			'pos'    => $pos,
			'substr' => substr($quote, $i, $pos - $i),
		);
	}
}

function bbcodeBold($dom, $nodes) {
	$b = $dom->createElement('b');
	bbcodeAppend($b, $nodes);
	return $b;
}

function bbcodeItalic($dom, $nodes) {
	$i = $dom->createElement('i');
	bbcodeAppend($i, $nodes);
	return $i;
}

function bbcodeUnderline($dom, $nodes) {
	$u = $dom->createElement('u');
	bbcodeAppend($u, $nodes);
	return $u;
}

function bbcodeStrikethrough($dom, $nodes) {
	$s = $dom->createElement('s');
	bbcodeAppend($s, $nodes);
	return $s;
}

function bbcodeURL($dom, $nodes, $arg) {
	$a = $dom->createElement('a');
	if ($arg === NULL)
	{
		$a->setAttribute('href', $nodes);
		$a->appendChild($dom->createTextNode($nodes));
	}
	else
	{
		$a->setAttribute('href', $arg);
		bbcodeAppend($a, $nodes);
	}
	return $a;
}

function bbcodeURLPRE($attr) {
	return $attr === NULL;
}

function bbcodeImage($dom, $nodes, $title) {
	$img = $dom->createElement('img');
	$img->setAttribute('src', $nodes);
	$img->setAttribute('title', $title);
	$img->setAttribute('class', 'imgtag');
	return $img;
}

function bbcodeImageScale($dom, $nodes, $title) {
	$a = $dom->createElement('a');
	$a->setAttribute('href', $nodes);
	$img = $dom->createElement('img');
	$img->setAttribute('src', $nodes);
	$img->setAttribute('title', $title);
	$img->setAttribute('class', 'imgtag');
	$img->setAttribute('style', 'max-width:300px; max-height:300px');
	$a->appendChild($img);
	return $a;
}

function bbcodeUser($dom, $nothing, $id) {
	return markupToMarkup($dom, UserLinkById((int) $id));
}

function bbcodeThread($dom, $nothing, $arg)
{
	global $threadLinkCache, $loguser;
	$id = (int)$arg;
	if(!isset($threadLinkCache[$id]))
	{
		$rThread = Query("SELECT
							t.id, t.title
						FROM {threads} t
						LEFT JOIN {forums} f ON t.forum = f.id
						WHERE t.id={0} AND f.minpower <= {1} ", $id, $loguser["powerlevel"]);
		if(NumRows($rThread))
		{
			$thread = Fetch($rThread);
			$threadLinkCache[$id] = makeThreadLink($thread);
		}
		else
			$threadLinkCache[$id] = "&lt;invalid thread ID&gt;";
	}
	return markupToMarkup($dom, $threadLinkCache[$id]);
}

function bbcodeForum($dom, $nothing, $arg)
{
	global $forumLinkCache, $loguser;
	$id = (int)$arg;
	if(!isset($forumLinkCache[$id]))
	{
		$rForum = Query("SELECT
							id, title
						FROM {forums}
						WHERE id={0} and minpower <= {1}", $id, $loguser["powerlevel"]);
		if(NumRows($rForum))
		{
			$forum = Fetch($rForum);
			$forumLinkCache[$id] = actionLinkTag($forum['title'], "forum", $forum['id']);
		}
		else
			$forumLinkCache[$id] = "&lt;invalid forum ID&gt;";
	}
	return markupToMarkup($dom, $forumLinkCache[$id]);
}

function bbcodeQuote($dom, $nodes, $arg, $attrs)
{
	return bbcodeQuoteGeneric($dom, $nodes, $arg, $attrs, __("Posted by"));
}

function bbcodeReply($dom, $nodes, $arg, $attrs)
{
	return bbcodeQuoteGeneric($dom, $nodes, $arg, $attrs, __("Sent by"));
}

function bbcodeQuoteGeneric($dom, $nodes, $arg, $attrs, $text)
{
	$div = $dom->createElement('div');
	$div->setAttribute('class', 'quote');
	if ($arg !== NULL)
	{
		$header = $dom->createElement('div');
		$header->setAttribute('class', 'quoteheader');

		if ($attrs['borked'])
		{
			$quote = parseQuoteLike($arg);
			$continue = $quote['pos'];
			$name = $quote['substr'];
			if (preg_match('/^\s*id\s*=\s*/', substr($arg, $continue), $matches))
			{
				$id = (int) parseQuoteLike($arg, strlen($matches[0]) + $continue, true);
				$user_name = $dom->createElement('a');
				$user_name->setAttribute('href', actionLink("post", $id));
				$user_name->appendChild($dom->createTextNode($name));
				$arg = $name;
			}
		}
		else
			$user_name = $dom->createTextNode($arg);
		$header->appendChild($dom->createTextNode("$text "));
		$header->appendChild($user_name);
		$div->appendChild($header);
	}
	$content = $dom->createElement('div');
	$content->setAttribute('class', 'quotecontent');
	bbcodeAppend($content, $nodes);
	$div->appendChild($content);
	return $div;
}

function bbcodeSpoiler($dom, $nodes, $arg) {
	$spoiler = $dom->createElement('div');
	$spoiler->setAttribute('class', 'spoiler');
	$button = $dom->createElement('button');
	if ($arg === NULL)
	{
		$button->setAttribute('class', 'spoilerbutton');
		$button->appendChild($dom->createTextNode('Show spoiler'));
	}
	else
	{
		$button->setAttribute('class', 'spoilerbutton named');
		$button->appendChild($dom->createTextNode($arg));
	}
	$spoiler->appendChild($button);
	$contents = $dom->createElement('div');
	$contents->setAttribute('class', 'spoiled hidden');
	bbcodeAppend($contents, $nodes);
	$spoiler->appendChild($contents);
	return $spoiler;
}
