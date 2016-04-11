<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Social_feed {

	private function getTwitter($limit = 10) {
    	$data = array();

    	$query = ee()->db->get('social_twitter', $limit);

    	foreach ($query->result() as $row) {
		    $data[] = array(
		    	'tweet' => $row->tweet,
		    	'url' => $row->url
		    );
		}

		return $data;
    }

    private function getYoutube($limit = 10) {
    	$data = array();

    	$query = ee()->db->get('social_youtube', $limit);

    	foreach ($query->result() as $row) {
		    $data[] = array(
		    	'image' => $row->image,
		    	'url' => $row->url
		    );
		}

		return $data;
    }

    private function getInstagram($limit = 10) {
    	$data = array();

    	$query = ee()->db->get('social_instagram', $limit);

    	foreach ($query->result() as $row) {
		    $data[] = array(
		    	'image' => $row->image,
		    	'url' => $row->url
		    );
		}

		return $data;
    }




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

		$consumerKey = '4d5WcS1Nj20iI7pqqND9c3j8i';
		$consumerSecret = 'pZbqkByjZN29Ae5UZwjX43v2GAFkltwuW1l9rLh8DaoJG2vmyE';
		$accessToken = '180814823-PU4jndLWWLn0ByzOkStD49fae6KA70ePCQmexTGb';
		$accessTokenSecret = 'RVc2tgdHfBlINwBZUVKhmgGUYhcewDyxugir9y5vaN8o7';

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

    	$data = $this->getYoutube($limit);

		return ee()->TMPL->parse_variables(ee()->TMPL->tagdata, $data);
    }

    // get instagram
    public function instagram() {
    	$defaultLimit = 10;

    	$limit = ee()->TMPL->fetch_param('limit', $defaultLimit);

    	$data = $this->getInstagram($limit);

		return ee()->TMPL->parse_variables(ee()->TMPL->tagdata, $data);
    }

    // get twitter
    public function twitter() {
    	$defaultLimit = 10;

    	$limit = ee()->TMPL->fetch_param('limit', $defaultLimit);

    	$data = $this->getTwitter($limit);

		return ee()->TMPL->parse_variables(ee()->TMPL->tagdata, $data);
    }

    


    public function to_json() {
    	$data = [];

    	$data["youtube"] = $this->getYoutube(10);
    	$data["instagram"] = $this->getInstagram(10);
    	$data["twitter"] = $this->getTwitter(10);

		return json_encode($data);
    }
}