<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Social_feed {
    // update youtube
    public function update_youtube() {
    	$url = "https://www.youtube.com/feeds/videos.xml?user=oystermarine";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$stream = curl_exec($ch);
		curl_close($ch);

		$xml = simplexml_load_string($stream);
		$ns = $xml->getNamespaces(true);

		ee()->db->truncate('social_youtube');

		foreach ( $xml->entry as $entry ) {
			$data = array(
                'title'       => (String) $entry->title,
				'image'       => (String) $entry->children($ns['media'])->group->children($ns['media'])->thumbnail->attributes()->url,
				'url'         => (String) $entry->link->attributes()->href
            );

	        ee()->db->insert('social_youtube', $data);
		}
    }

    // update instagram
    public function update_instagram() {
    	$url = "https://www.instagram.com/oysteryachts/media/";

    	$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$stream = curl_exec($ch);
		curl_close($ch);

		$streamDecoded = json_decode($stream);

		ee()->db->truncate('social_instagram');

		foreach ($streamDecoded->items as $entry) {
			$data = array(
				'title'       => $entry->caption->text,
                'image'       => $entry->images->standard_resolution->url,
				'url'         => $entry->link
            );

	        ee()->db->insert('social_instagram', $data);
		}
    }

    // update twitter
    public function update_twitter() {
    	require "libraries/twitteroauth/autoload.php";

		$consumerKey = 'YNjvlEk1W699d7ZizPqmgE4D1';
		$consumerSecret = '7Z4PmOlvuS3dl1Gxzo8H7Dix2AIZE6NYk4y56N7cSAQrgoZ9yA';
		$accessToken = '122032530-46fb61LeusIszAyXoZtYmUD08aDI0BxOVz3kkIv2';
		$accessTokenSecret = 'ONXKkCIIOCHSjTz3zoCc4sGtqOA9QnkqToAOJZULvRNLZ';

		$connection = new Abraham\TwitterOAuth\TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
		//$content = $connection->get("account/verify_credentials");

		$statuses = $connection->get("statuses/user_timeline", ["screen_name" => "OysterMarine", "count" => 20, "exclude_replies" => true]);

		ee()->db->truncate('social_twitter');

		foreach ($statuses as $entry) {
			$data = array(
				'tweet'       => $entry->text,
				'url'         => 'https://twitter.com/statuses/'.$entry->id
            );

	        ee()->db->insert('social_twitter', $data);
		}
    }

    // get youtube
    public function youtube() {
    	$defaultLimit = 10;

    	$limit = ee()->TMPL->fetch_param('limit', $defaultLimit);

    	$variables = array();

    	$query = ee()->db->get('social_youtube', $limit);

    	foreach ($query->result() as $row) {
		    $variables[] = array(
		    	'title' => $row->title,
		    	'image' => $row->image,
		    	'url' => $row->url
		    );
		}

		return ee()->TMPL->parse_variables(ee()->TMPL->tagdata, $variables);
    }

    // get instagram
    public function instagram() {
    	$defaultLimit = 10;

    	$limit = ee()->TMPL->fetch_param('limit', $defaultLimit);

    	$variables = array();

    	$query = ee()->db->get('social_instagram', $limit);

    	foreach ($query->result() as $row) {
		    $variables[] = array(
		    	'title' => $row->title,
		    	'image' => $row->image,
		    	'url' => $row->url
		    );
		}

		return ee()->TMPL->parse_variables(ee()->TMPL->tagdata, $variables);
    }

    // get twitter
    public function twitter() {
    	$defaultLimit = 10;

    	$limit = ee()->TMPL->fetch_param('limit', $defaultLimit);

    	$variables = array();

    	$query = ee()->db->get('social_twitter', $limit);

    	foreach ($query->result() as $row) {
		    $variables[] = array(
		    	'tweet' => $row->tweet,
		    	'url' => $row->url
		    );
		}

		return ee()->TMPL->parse_variables(ee()->TMPL->tagdata, $variables);
    }
}