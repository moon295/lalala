<?php
/**
 *  Batch/Getyelpshops.php
 *
 *  @author     {$author}
 *  @package    Lpo
 *  @version    $Id: skel.action_cli.php 387 2006-11-06 14:31:24Z cocoitiban $
 */
/**
 *  batch_getyelpshopsフォームの実装
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Cli_Form_BatchGetyelpshops extends Lpo_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = true;

    /**
     *  @access private
     *  @var    array   フォーム値定義
     */
    var $form = array(
        /*
        'sample' => array(
            // フォームの定義
            'type'          => VAR_TYPE_INT,    // 入力値型
            'form_type'     => FORM_TYPE_TEXT,  // フォーム型
            'name'          => 'サンプル',      // 表示名
            // バリデータ(記述順にバリデータが実行されます)
            'required'      => true,            // 必須オプション(true/false)
            'min'           => null,            // 最小値
            'max'           => null,            // 最大値
            'regexp'        => null,            // 文字種指定(正規表現)
            // フィルタ
            'filter'        => null,            // 入力値変換フィルタオプション
        ),
        */
    );
}
/**
 *  batch_getyelpshopsアクションの実装
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Cli_Action_BatchGetyelpshops extends Lpo_ActionClass
{
    var $CONSUMER_KEY     = 'oHh7h2CoYG2SNsQwvIP-Og';
    var $CONSUMER_SECRET  = 'OcpXVvMGawuAaD_cFC1w9Q7Iz2s';
    var $TOKEN            = 'BAjP9a4gNJheKL3bWRHoO8OZODWchrVq';
    var $TOKEN_SECRET     = '1OJGWi35Ve73wiEnfbLzA1Hk9J8';
    var $API_HOST         = 'api.yelp.com';
    var $DEFAULT_TERM     = '';
    var $DEFAULT_LOCATION = '5300041';
    var $SEARCH_LIMIT     = 20;
    var $SEARCH_PATH      = '/v2/search/';
    var $BUSINESS_PATH    = '/v2/business/';
    var $apiCount         = 0;

    /**
     *  batch_getyelpshopsアクションの前処理
     *
     *  @access public
     *  @return string      遷移名(正常終了ならnull, 処理終了ならfalse)
     */
    function prepare()
    {
        echo "START Getyelpshops:" . date('Y-m-d H:i:s') . "\n";
        return null;
    }
    /**
     *  batch_getyelpshopsアクションの実装
     *
     *  @access public
     *  @return string  遷移名
     */
    function perform()
    {
        $this->queryApi();

        echo "END BatchGetyelpshops:" . date('Y-m-d H:i:s') . "\n";
        return null;
    }

    function queryApi()
    {
        $searchResponse = json_decode($this->search());	//apiコールでリスト取得一回無駄。。。
        $total = $searchResponse->total;
echo $total."\n";

        for ($i = 1; $i < $total; $i = $i + $this->SEARCH_LIMIT) {
            if ($this->apiCount > 9000) exit;
            $searchResponse = json_decode($this->search($i));
            foreach ($searchResponse->businesses AS $key => $value) {
                $businessResponse = json_decode($this->get_business(urlencode($value->id)));
                $this->register($businessResponse);
            }
        }
    }

    function register($businessResponse)
    {
//pr($businessResponse);
//echo "\n\n\n\n";
echo $businessResponse->name."\n";
        $shopManager         = new ShopManager();
        $categoryManager     = new CategoryManager();
        $shopcategoryManager = new ShopcategoryManager();

        $shopSearch = array('yelp_id' => $businessResponse->id);
        if (!is_array($shopManager->get($shopSearch))) {
            $shopInsert = array(
                'yelp_id'              => $businessResponse->id,
                'name'                 => $businessResponse->name,
                'image_url'            => $businessResponse->image_url,
                'rating'               => $businessResponse->rating,
                'rating_img_url'       => $businessResponse->rating_img_url,
                'rating_img_url_small' => $businessResponse->rating_img_url_small,
                'url'                  => $businessResponse->url,
                'mobile_url'           => $businessResponse->mobile_url,
                //'snippet_image_url'    => $businessResponse->snippet_image_url,
                //'snippet_text'         => $businessResponse->snippet_text,
                //'phone'                => $businessResponse->phone,
                //'postal_code'          => str_replace("-", "", $businessResponse->location->postal_code),
                'address1'             => $businessResponse->location->address[0],
                'lat'                  => $businessResponse->location->coordinate->latitude,
                'lng'                  => $businessResponse->location->coordinate->longitude,
                'is_closed'            => (int) $businessResponse->is_closed,
                '#created'             => 'NOW()',
            );
            if (isset($businessResponse->location->address[1])) {
                $shopInsert['address2'] = $businessResponse->location->address[1];
            }
            if (isset($businessResponse->location->postal_code)) {
                $shopInsert['postal_code'] = $businessResponse->location->postal_code;
            }
            if (isset($businessResponse->location->postal_code)) {
                $shopInsert['snippet_image_url'] = $businessResponse->snippet_image_url;
            }
            if (isset($businessResponse->snippet_text)) {
                $shopInsert['snippet_text'] = $businessResponse->snippet_text;
            }
            if (isset($businessResponse->phone)) {
                $shopInsert['phone'] = $businessResponse->phone;
            }
            $shopID = $shopManager->insert($shopInsert);
            foreach ($businessResponse->categories as $category) {
                $categorySearch = array('name' => $category[0]);
                $categoryInfo = $categoryManager->get($categorySearch);
                if (!is_array($categoryInfo)) {
                    $categoryInsert = array(
                        'name' => $category[0]
                    ); 
                    $categoryID = $categoryManager->insert($categoryInsert);
                } else {
                    $categoryID = $categoryInfo['id'];
                }
                $shopcategoryInsert = array(
                    'shop_id'     => $shopID,
                    'category_id' => $categoryID,
                ); 
                $shopcategoryManager->insert($shopcategoryInsert);
                
            }
        }
    }

    function search($offset = 0)
    {
        $url_params = array();

        $url_params['location']      = urlencode($this->DEFAULT_LOCATION);
        $url_params['limit']         = $this->SEARCH_LIMIT;
        $url_params['lang']          = 'ja';
        $url_params['radius_filter'] = 265;
        $url_params['offset']        = $offset == 0 ? '0' : $offset;
    	$search_path = $this->SEARCH_PATH . "?" . http_build_query($url_params);

    	return $this->request($this->API_HOST, $search_path);
    }

    function request($host, $path) {
        $this->apiCount++;
        $unsigned_url = "http://" . $host . $path;
    
        // Token object built using the OAuth library
        $token = new OAuthToken($this->TOKEN, $this->TOKEN_SECRET);
    
        // Consumer object built using the OAuth library
        $consumer = new OAuthConsumer($this->CONSUMER_KEY, $this->CONSUMER_SECRET);
    
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

        sleep(1);

        return $data;
    }

    function get_business($business_id) {
        $business_path = $this->BUSINESS_PATH . $business_id . "?lang=ja";
    
        return $this->request($this->API_HOST, $business_path);
    }
}
?>
