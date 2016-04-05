<?php

$url = "https://www.youtube.com/feeds/videos.xml?user=oystermarine";

$stream = file_get_contents($url);

$xml = simplexml_load_string($stream);
$ns = $xml->getNamespaces(true);

echo '<pre>';

var_dump($ns);

foreach ( $xml->entry as $entry ) :
	echo $entry->title;

	echo '<br />';

	/*foreach ( $entry->getNameSpaces( true ) as $key => $children )
	$$key = $entry->children( $children );

	print_r($media->thumbnail);*/

	//echo $media->thumbnail;

	$media_group = $entry->children($ns['media'])->group;

	$media_url = $media_group->children($ns['media'])->thumbnail->attributes()->url;

	echo 'Thumb: '.$media_url.'<br />';
	echo 'Link: '.$entry->link->attributes()->href.'<br /><br />';

	//echo $media_url;

endforeach;

/*$p = xml_parser_create();
xml_parse_into_struct($p, $stream, $vals, $index);
xml_parser_free($p);

echo '<pre>';

//var_dump($vals);

var_dump($index);*/

/*$xml = new SimpleXMLElement($stream);
$xml = dom_import_simplexml($xml);
$nodelist= $xml->getElementsByTagName('event');  
for($i = 0; $i < $nodelist->length; $i++) {
    $sessions = $nodelist->item($i)->getElementsByTagName('thumbnail');
    echo $sessions->item(0)->nodeValue;
}*/


/*$xml = simplexml_load_file($url, "SimpleXMLElement", LIBXML_NOCDATA);
$json = json_encode($xml);
$array = json_decode($json,TRUE);*/



/*foreach ($xml->children($ns['media'])->media as $skey) {
	echo 'asd';

    $thumb = $skey->children($ns['media'])->thumbnail;
    echo $thumb;
}*/


/*$obj->getElementByTagName('entry');
$value = $obj->nodeValue();

echo $value;*/

//echo '<pre>';

//var_dump($stream);
?>