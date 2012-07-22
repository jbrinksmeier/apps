<?php
/**
* ownCloud - News app
*
* @author Alessandro Cosentino
* Copyright (c) 2012 - Alessandro Cosentino <cosenal@gmail.com>
*
* This file is licensed under the Affero General Public License version 3 or later.
* See the COPYING-README file
*
*/

// load SimplePie library
//TODO: is this file a suitable place for the following require?
 require_once('news/3rdparty/SimplePie/autoloader.php');

class OC_News_Utils {

	/**
	 * @brief Fetch a feed from remote
	 * @param url remote url of the feed
	 * @returns
	 */
	public static function fetch($url){
	//TODO: handle the case where fetching of the feed fails
		$spfeed = new SimplePie_Core();
		$spfeed->set_feed_url( $url );
		$spfeed->enable_cache( false );
		$spfeed->init();
		$spfeed->handle_content_type();
		$title = $spfeed->get_title();

		$spitems = $spfeed->get_items();
		$items = array();
		foreach($spitems as $spitem) { //FIXME: maybe we can avoid this loop
			$itemUrl = $spitem->get_permalink();
			$itemTitle = $spitem->get_title();
			$itemGUID = $spitem->get_id();
			$itemBody = $spitem->get_content();
			$items[] = new OC_News_Item($itemUrl, $itemTitle, $itemGUID, $itemBody);
		}

		$feed = new OC_News_Feed($url, $title, $items);

		$favicon = $spfeed->get_image_url();
		//check if this file exists and the size with getimagesize()

		if ($favicon == null) {
			//handle favicon detection
			$favicon = SimplePie_Misc::absolutize_url('/favicon.ico', $url);
			// get file
			$file = new SimplePie_File($favicon);
			$sniffer = new SimplePie_Content_Type_Sniffer($file);
			// check file
			if(substr($sniffer->get_type(), 0, 6) !== 'image/')
				$favicon = null;
		}

		$feed->setFavicon($favicon);

		return $feed;
	}
}