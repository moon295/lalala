<?php

/**
 * Yelp API v2.0 code sample.
 *
 * This program demonstrates the capability of the Yelp API version 2.0
 * by using the Search API to query for businesses by a search term and location,
 * and the Business API to query additional information about the top result
 * from the search query.
 * 
 * Please refer to http://www.yelp.com/developers/documentation for the API documentation.
 * 
 * This program requires a PHP OAuth2 library, which is included in this branch and can be
 * found here:
 *      http://oauth.googlecode.com/svn/code/php/
 * 
 * Sample usage of the program:
 * `php sample.php --term="bars" --location="San Francisco, CA"`
 */

// Enter the path that the oauth library is in relation to the php file
require_once('/var/www/lalala/lib/util/Yelp.php');

// Set your OAuth credentials here  
// These credentials can be obtained from the 'Manage API Access' page in the
// developers documentation (http://www.yelp.com/developers)
$CONSUMER_KEY = 'oHh7h2CoYG2SNsQwvIP-Og';
$CONSUMER_SECRET = 'OcpXVvMGawuAaD_cFC1w9Q7Iz2s';
$TOKEN = 'BAjP9a4gNJheKL3bWRHoO8OZODWchrVq';
$TOKEN_SECRET = '1OJGWi35Ve73wiEnfbLzA1Hk9J8';


$API_HOST = 'api.yelp.com';
$DEFAULT_TERM = '';
$DEFAULT_LOCATION = '5300041';
$SEARCH_LIMIT = 20;
$SEARCH_PATH = '/v2/search/';
$BUSINESS_PATH = '/v2/business/';


/** 
 * Makes a request to the Yelp API and returns the response
 * 
 * @param    $host    The domain host of the API 
 * @param    $path    The path of the APi after the domain
 * @return   The JSON response from the request      
 */
function request($host, $path) {
    $unsigned_url = "http://" . $host . $path;

    // Token object built using the OAuth library
    $token = new OAuthToken($GLOBALS['TOKEN'], $GLOBALS['TOKEN_SECRET']);

    // Consumer object built using the OAuth library
    $consumer = new OAuthConsumer($GLOBALS['CONSUMER_KEY'], $GLOBALS['CONSUMER_SECRET']);

    // Yelp uses HMAC SHA1 encoding
    $signature_method = new OAuthSignatureMethod_HMAC_SHA1();

    $oauthrequest = OAuthRequest::from_consumer_and_token(
        $consumer, 
        $token, 
        'GET', 
        $unsigned_url
    );
    
    // Sign the request
    $oauthrequest->sign_request($signature_method, $consumer, $token);
    
    // Get the signed URL
    $signed_url = $oauthrequest->to_url();
    
    // Send Yelp API Call
    $ch = curl_init($signed_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $data = curl_exec($ch);
    curl_close($ch);
    
    return $data;
}

/**
 * Query the Search API by a search term and location 
 * 
 * @param    $term        The search term passed to the API 
 * @param    $location    The search location passed to the API 
 * @return   The JSON response from the request 
 */
function search($term, $location) {
    $url_params = array();
    
    //$url_params['term'] = $term ?: $GLOBALS['DEFAULT_TERM'];
    $url_params['location'] = $location?: urlencode($GLOBALS['DEFAULT_LOCATION']);
    $url_params['limit'] = $GLOBALS['SEARCH_LIMIT'];
    $url_params['lang'] = 'ja';
    //$url_params['category'] = '';
    $search_path = $GLOBALS['SEARCH_PATH'] . "?" . http_build_query($url_params);
    
    return request($GLOBALS['API_HOST'], $search_path);
}

/**
 * Query the Business API by business_id
 * 
 * @param    $business_id    The ID of the business to query
 * @return   The JSON response from the request 
 */
function get_business($business_id) {
    $business_path = $GLOBALS['BUSINESS_PATH'] . $business_id . "?lang=ja";
    
    return request($GLOBALS['API_HOST'], $business_path);
}

/**
 * Queries the API by the input values from the user 
 * 
 * @param    $term        The search term to query
 * @param    $location    The location of the business to query
 */
function query_api($term, $location) {     
    $searchResponse = json_decode(search($term, $location));
var_dump($searchResponse->total);
    foreach ($searchResponse->businesses AS $key => $value) {
        $businessResponse = json_decode(get_business(urlencode($value->id)));
var_dump($businessResponse);
        echo $businessResponse->id;	// YelpID
        echo "<br>\n";
        echo $businessResponse->name;	// ショップ名
        echo "<br>\n";
        echo $businessResponse->rating;	// rating
        echo "<br>\n";
        printf("<a href='%s'>yelp PC page</a>", $businessResponse->url);	// URL
        echo "<br>\n";
        printf("<a href='%s'>yelp Mobile page</a>", $businessResponse->mobile_url);	// 携帯URL
        echo "<br>\n";
        printf("<img src='%s' />", $businessResponse->rating_img_url);	// 評価画像
        echo "<br>\n";
        printf("<img src='%s' />", $businessResponse->rating_img_url_small);	// 評価画像小
        echo "<br>\n";
        printf("<img src='%s' />", $businessResponse->snippet_image_url);	// ?
        echo "<br>\n";
        echo $businessResponse->phone;	// 電話番号
        echo "<br>\n";
        echo $businessResponse->snippet_text;	// snippet_text
        echo "<br>\n";
        printf("<img src='%s' />", $businessResponse->image_url);	// ?
        echo "<br>\n";
        foreach ($businessResponse->categories as $value) {
            echo $value[0];
            echo "<br>\n";
        }
        var_dump($businessResponse->is_closed);	// 閉店してる？？
        echo "<br>\n";
        var_dump($businessResponse->location->postal_code);
        var_dump($businessResponse->location->address);
        var_dump($businessResponse->location->coordinate);
        echo "<br>\n";

exit;
        //sleep(1);
    }
/*
    $business_id = $response->businesses[0]->id;
    
    print sprintf(
        "%d businesses found, querying business info for the top result \"%s\"\n\n",         
        count($response->businesses),
        $business_id
    );
    
    $response = get_business(urlencode($business_id));
    
    print sprintf("Result for business \"%s\" found:\n", $business_id);
    print "$response\n";
*/
}

/**
 * User input is handled here 
 */
$longopts  = array(
    "term::",
    "location::",
);
    
$options = getopt("", $longopts);

$term = $options['term'] ?: '';
$location = $options['location'] ?: '';

query_api($term, $location);

?>
