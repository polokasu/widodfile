<?php
if  (isset($_GET['num'])) {
	$num = $_GET['num'];
} else {
	$num = 6;
}
$tags = (isset($_GET['tags']) ? $_GET['tags'] : '');
if (strlen($tags) === 0) {
	exit('&nbsp;');
}
if (isset($_GET['max'])) {
	define('MAX_RESULT_FEED', (int)$_GET['max']);
} else {
	define('MAX_RESULT_FEED', 50);
}
$blogger_feed_url = 'https://gendol-online.blogspot.com/feeds/posts/default?q=%s&alt=rss&max-results=%d';
$feed_url = sprintf($blogger_feed_url, $tags, MAX_RESULT_FEED);
$feed = new DOMDocument();

$feed->load($feed_url);
$items = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('item');
$i = 0;
$json = array();
foreach($items as $item) {
$id= $item->getElementsByTagName('guid')->item(0)->firstChild->nodeValue;
   $title = $item->getElementsByTagName('title')->item(0)->firstChild->nodeValue;
   $link = $item->getElementsByTagName('link')->item(0)->firstChild->nodeValue;
   if ($item->getElementsByTagNameNS('http://search.yahoo.com/mrss/','thumbnail')->item(0) == true) {
$image= $item->getElementsByTagNameNS('http://search.yahoo.com/mrss/','thumbnail')->item(0)->getAttribute('url');
}  else {
$image = '';
}
	$post[$id] = array(
   'title' =>  $title,
   'link' =>  $link,
   'image' =>  str_replace("\/","/",$image)
);
};
	header('Content-type: text/plain; charset=utf-8');
	header("Access-Control-Allow-Origin: *");
	header("AMP-Same-Origin: true");
	header("Access-Control-Expose-Headers: AMP-Access-Control-Allow-Source-Origin");
	header("AMP-Access-Control-Allow-Source-Origin:");
	$all_relateds = array_values($post);
	$max_relateds = count($all_relateds);
if ($max_relateds>$num) {
	$related = array_rand($all_relateds,$num);
	echo('{"items":[');
for($x = 1; $x < $num; $x++) {
    echo str_replace("http:","https:",json_encode($all_relateds[$related[$x]]));echo (',');
}
       echo str_replace("http:","https:",json_encode($all_relateds[$related[0]]));
	echo(']}');
} elseif (1<$max_relateds) {
	$related = array_rand($all_relateds,$max_relateds);
	echo('{"items":[');
for($x = 1; $x < $max_relateds; $x++) {
    echo str_replace("http:","https:",json_encode($all_relateds[$related[$x]]));echo (',');
}
    echo str_replace("http:","https:",json_encode($all_relateds[$related[0]]));
	echo(']}');
} else {
	echo('{"items":');
echo str_replace("http:","https:",json_encode($all_relateds));
	echo('}');
}
?>
