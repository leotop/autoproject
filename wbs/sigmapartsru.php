<?php

class sigmapartsru extends SearchController {
	
	private $config;
	
	function __construct(){
		$config = WbsModel::getConfig(basename(__FILE__));
		$this->config = $config;
		$this->imp_id = $config['id'];
	}
	
	function searchAll($serialize=true,$account=array()) {
		
		$CORRECT_BRANDS = $this->wbs_correct_brandnames();
		$INCORRECT_NAMES = $CORRECT_NAMES = array();
		if (isset($CORRECT_BRANDS) && count($CORRECT_BRANDS)>0){
			foreach ($CORRECT_BRANDS as $INCORRECT){
				$INCORRECT_NAMES []= $INCORRECT['incorrect'];
				$CORRECT_NAMES [$INCORRECT['incorrect']]= $INCORRECT['correct'];
			}
		}
		
		/* ***************************** */
		
		$ARTICLE_REGET = isset($_REQUEST['article'])?$_REQUEST['article']:false;
		$BRAND_REGET_ID = isset($_REQUEST['brand'])?$_REQUEST['brand']:false;
		$BrandsModel = BrandsModel::getById($BRAND_REGET_ID);
		if ($BrandsModel){
			$eval = $BrandsModel['BRA_BRAND'];
		}else{
			$eval = $BRAND_REGET_ID;
		}
		
		/* ***************************** */
				
		$ARTICLE = isset($_REQUEST['article'])?$_REQUEST['article']:false;
		$GROUP_ID = isset($_REQUEST['brand'])?$_REQUEST['brand']:false;
		$Q = ($this->config['is_groups'])?$GROUP_ID:$ARTICLE;
		$GROUP_ID = isset($_REQUEST['option'][$this->config['id']])?($_REQUEST['option'][$this->config['id']]):$Q;
		
		$SigmaSoapClient = new SigmaSoapClient();
		$result = $SigmaSoapClient->findDetail($this->config['login'], $this->config['pass'], FuncModel::stringfilter($ARTICLE));
		$result = mb_convert_encoding($result, 'UTF-8', 'auto');
// 		$result = str_replace('&','', $result);
		$priceOnlineItems = simplexml_load_string($result);
				
		if (isset($priceOnlineItems)&&count($priceOnlineItems)>0){
		$i=0; foreach ($priceOnlineItems as $key=>$Ar){ $i++;
			
			$Ar = (array)$Ar;
			
// 			echo('<pre>');
// 			var_dump($Ar);
// 			echo('</pre>');
// 			exit();
			
			$ID = "wbs-sigma-".$i;
			$IMPORT_ID = $this->config['importer_id'];
			$BRAND_ID = 0;
			
			$BRAND_NAME = strtoupper($Ar['owner']);
			$ARTICLE = strtoupper($Ar['code']);
			$PRICE = strtoupper($Ar['price']);
			$DESCR = strtoupper($Ar['title']);
			$BOX = strtoupper($Ar['nalreal']);
			$DELIVERY = strtoupper($Ar['day1']);
			
			$WEIGHT = "";
			$IMG_URL = "";
			
// 			var_dump($ID,$IMPORT_ID,$BRAND_ID,$BRAND_NAME,$ARTICLE,$PRICE,$DESCR,$BOX,$DELIVERY,$WEIGHT,$IMG_URL);
// 			exit();
		
			/* ***************** */
			if (isset($INCORRECT_NAMES) && in_array($BRAND_NAME,$INCORRECT_NAMES)) {
				$BRAND_NAME = (isset($CORRECT_NAMES[$BRAND_NAME])?$CORRECT_NAMES[$BRAND_NAME]:$BRAND_NAME);
			}
			$isBOX = true;
			if ($this->config['param_typeview'] == 1){
				if ((int)$BOX > 0 || $BOX === 'есть' || $BOX === '+')
					$isBOX = true;
				else 
					$isBOX = false;
			}
		
			if ($isBOX){
				$IMPORTER_DATA = $this->ImportersModel_getById($IMPORT_ID);
				$OUTPRICE_DATA = OutpriceModel::generate($IMPORTER_DATA,($account)?$account:$this->getAccountData(),$PRICE,$BRAND_NAME);
				$ORIGINAL = 0;
				list($str_check,) = explode(" ",$eval);
				if(preg_match('/('.$str_check.')/i', $BRAND_NAME) && (FuncModel::stringfilter($ARTICLE) == FuncModel::stringfilter($ARTICLE_REGET))){
					$ORIGINAL = 1;
				}
				$d= array(
					'ORIGINAL'	=>	$ORIGINAL,
					'ART_ID' => 0,
					'DB_IMPORT_ID' => $IMPORT_ID,
					'SUP_ID' => $BRAND_ID,
					'SUP_BRAND' => $BRAND_NAME,
					'ART_ARTICLE_NR' => $ARTICLE,
					'ART_ARTICLE_NR_CLEAR' => FuncModel::stringfilter($ARTICLE),
					'DB_PRICE' => $PRICE,
					'TEX_TEXT' => $DESCR,
					'SUP_UNICODE_BRAND'=>'',
					'CRITERIA'=>array(),
					'PATH_IMAGES'=>array(),
					'PATH_LOGOS'=>array(),
					'PRICES'=>array(),
					'FR'=>array(),
					
					'DB_ID' => $ID,
					'DB_IMPORT_ID' => $IMPORT_ID,
					'DB_BRAND_ID' => $BRAND_ID,
					'DB_BRAND_NAME' => $BRAND_NAME,
					'DB_ARTICLE' => $ARTICLE,
					'DB_PRICE' => $PRICE,
					'DB_DESCR' => $DESCR,
					'DB_BOX' => $BOX,
					'DB_DELIVERY' => ($DELIVERY+$IMPORTER_DATA['delivery']),
					'DB_WEIGHT' => $WEIGHT,
					'DB_IMG_URL' => $IMG_URL,
					
					'MY_PRICE'=>array(	'ID' 			=> $ID,
										'IMPORT_ID' 	=> $IMPORT_ID,
										'BRAND_ID' 		=> $BRAND_ID,
										'BRAND_NAME' 	=> $BRAND_NAME,
										'ARTICLE' 		=> $ARTICLE,
										'PRICE' 		=> $PRICE,
										'DESCR' 		=> $DESCR,
										'BOX' 			=> $BOX,
										'DELIVERY' 		=> ($DELIVERY+$IMPORTER_DATA['delivery']),
										'WEIGHT' 		=> $WEIGHT,
										'IMG_URL' 		=> $IMG_URL,
										'IMPORTER_DATA'	=>	$IMPORTER_DATA,
										'OUTPRICE_DATA'	=>	$OUTPRICE_DATA,
										'RESULT_PRICE_SALE'	=>	(($IMPORTER_DATA['currency'])?($IMPORTER_DATA['currency']*$OUTPRICE_DATA['resultPRICE']):$OUTPRICE_DATA['resultPRICE']),
										),
										
					'ID' 			=> $ID,
					'IMPORT_ID' 	=> $IMPORT_ID,
					'BRAND_ID' 		=> $BRAND_ID,
					'BRAND_NAME' 	=> $BRAND_NAME,
					'ARTICLE' 		=> $ARTICLE,
					'PRICE' 		=> $PRICE,
					'DESCR' 		=> $DESCR,
					'BOX' 			=> $BOX,
					'DELIVERY' 		=> ($DELIVERY+$IMPORTER_DATA['delivery']),
					'WEIGHT' 		=> $WEIGHT,
					'IMG_URL' 		=> $IMG_URL,
					'IS_CROSS'		=>	0,
					'IS_ACCOUNT'	=>	0,
					'IMPORTER_DATA'	=>	$IMPORTER_DATA,
					'OUTPRICE_DATA'	=>	$OUTPRICE_DATA,
					'RESULT_PRICE_SALE'	=>	(($IMPORTER_DATA['currency'])?($IMPORTER_DATA['currency']*$OUTPRICE_DATA['resultPRICE']):$OUTPRICE_DATA['resultPRICE']),
				);
				if ($serialize){
					$d = (object)$d;
					$dataInDB []= serialize($d);
				}
				else {
					$dataInDB []= ($d);	
				}
			}
			/* ***************** */
		}}
				
		return $dataInDB;
	}
	
	function FindCatalog($detailNum=''){ 
		return array();
	}
}

/**
 * @service SigmaSoapClient
 */
class SigmaSoapClient{
	/**
	 * The WSDL URI
	 *
	 * @var string
	 */
	public static $_WsdlUri='http://online.sigma-parts.ru/main/wsdl.php?WSDL';
	/**
	 * The PHP SoapClient object
	 *
	 * @var object
	 */
	public static $_Server=null;

	/**
	 * Send a SOAP request to the server
	 *
	 * @param string $method The method name
	 * @param array $param The parameters
	 * @return mixed The server response
	 */
	public static function _Call($method,$param){
		if(is_null(self::$_Server))
			self::$_Server=new SoapClient(self::$_WsdlUri,array('connection_timeout'=>10));
		return self::$_Server->__soapCall($method,$param);
	}

	/**
	 * @param string $login
	 * @param string $pass
	 * @param string $code
	 * @return string
	 */
	public function findDetail($login,$pass,$code){
		return self::_Call('findDetail',Array(
				$login,
				$pass,
				$code
		));
	}
}

?>