<?php
/*

Da es aktuell noch keine RSS Feeds der Audiothek gibt, basteln wir uns hiermit einen Feed.
Beispiel: audiothekfeed.php?id=71595786

Lizenz: Eine do-what-the-f*-you-want-Lizenz. -> The Unlicence

This is free and unencumbered software released into the public domain.

Anyone is free to copy, modify, publish, use, compile, sell, or
distribute this software, either in source code form or as a compiled
binary, for any purpose, commercial or non-commercial, and by any
means.

In jurisdictions that recognize copyright laws, the author or authors
of this software dedicate any and all copyright interest in the
software to the public domain. We make this dedication for the benefit
of the public at large and to the detriment of our heirs and
successors. We intend this dedication to be an overt act of
relinquishment in perpetuity of all present and future rights to this
software under copyright law.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR
OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

For more information, please refer to <http://unlicense.org/>

*/

 if(!isset($_GET['id'])){exit;}
 else
 {
	 $id = intval($_GET['id']);
 }

$curl = curl_init();
 
curl_setopt($curl, CURLOPT_URL,
    base64_decode('aHR0cHM6Ly9hcGkuYXJkYXVkaW90aGVrLmRlL3Byb2dyYW1zZXRzLw').$id);
   
curl_setopt($curl,
    CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($curl);
 
if($e = curl_error($curl)) {
    echo $e;exit;
} else {
     
    // Decoding JSON data
    $decodedData = json_decode($response, true);
	   
if(empty($decodedData)){exit;}
if(isset($decodedData['data']))
{
	if(empty($decodedData['data']["programSet"]))	{exit;};
}   
										 
	//for debugging
    //echo '<pre>';print_r($decodedData);
	
	function cleanup($string)
	{
		$string = str_replace('{width}',200,$string);
		$string = str_replace(':',urlencode(':'),$string);
		$string = str_replace('&',urlencode('&'),$string);
		$string = str_replace('https%3A','https:',$string);
		
		return($string);
	}
	
	$info = $decodedData['data']['programSet'];
	$Name_vom_Feed = htmlentities($info['title']);
	$Link_zur_Webseite = $info['sharingUrl'];
	$Beschreibung_des_Feeds = htmlentities($info['synopsis']);
	$Titelbild = cleanup($info['image']['url']);
	$Generator_Name = 'Wolfcreator';
	$Sprache = 'de';


$rss[] = '<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" version="2.0">
<channel>
<title>
<![CDATA[ '.$Name_vom_Feed.' ]]>
</title>
<link>'.$Link_zur_Webseite.'</link>
<description>
<![CDATA[ '.$Beschreibung_des_Feeds.' ]]>
</description>
<pubDate>'.$info['items']['nodes'][0]['publicationStartDateAndTime'].'</pubDate>
<generator>'.$Generator_Name.'</generator>
<language>'.$Sprache.'</language>
<image>';
/*
Die Bild-URL funktioniert derzeit noch nicht.
//str_replace('{width}',200,$item['image']['url']
*/
$rss[] = '<url></url>
<title>'.$Name_vom_Feed.'</title>
<link>'.$Link_zur_Webseite.'</link>
</image>
<docs>http://blogs.law.harvard.edu/tech/rss</docs>';

for($a=0;$a<count($info['items']['nodes']);$a++)

{
	$item = $info['items']['nodes'][$a];
	$rss[] =  '<item>
<title>
<![CDATA[ '.$item['title'].' ]]>
</title>
<image>
<url></url>';
/*
Die Bild-URL funktioniert derzeit noch nicht.
	str_replace('{width}',200,$item['image']['url']
*/
$rss[] = '<title><![CDATA['.htmlentities($item['title']).']]></title>
<link>'.$item['sharingUrl'].'</link>
</image>
<link>'.$item['sharingUrl'].'</link>
<enclosure url="'.$item['audios'][0]['url'].'" length="'.$item['duration'].'" type="audio/mpeg"/>
<guid>'.base64_encode($item['publicationStartDateAndTime']).'</guid>
<description>
<![CDATA['.$item['synopsis'].']]>
</description>
<category>autoplay</category>
<pubDate>'.$item['publicationStartDateAndTime'].'</pubDate>
</item>';
}
$rss[] = '</channel>
</rss>';

$output = implode("\n",$rss);
echo $output;
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
 
// Closing curl
curl_close($curl);
?>