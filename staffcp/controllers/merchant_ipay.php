<?php

class Merchant_ipayController  extends CmsGenerator {
	
	public function index() {
		$db = Register::get('db');
		$sql = "SELECT * FROM ".DB_PREFIX."settings_merchants WHERE `group`='IPAY'";
		$this->view->data = $db->query($sql);
		
		if (isset($_POST['save']) && $_POST['save']){
			Logs::addLog(Acl::getAuthedUserId(),'Настройка экварийнга IPAY',URL_NOW);
			$code = $this->request("code");
			if (isset($code) && count($code)>0){
				foreach ($code as $id => $item){
					$db->post("UPDATE ".DB_PREFIX."settings_merchants SET `value`='".mysql_real_escape_string($item)."' WHERE id='".(int)$id."';");
				}
			}
			$this->redirectUrl('/staffcp/merchant_ipay/');
		}
		
		$delete = $this->request("delete",false);
		if ($delete){
			$this->delete($delete);
			$this->redirectUrl('/staffcp/merchant_ipay/');
		}
		
		/* ************************** */
		
		$sql = "SELECT * FROM ".DB_PREFIX."settings_merchants_result WHERE `merchant` = 'IPAY' ORDER BY id DESC LIMIT 0,200";
		$this->view->bills = $db->query($sql);
	}
	
	private function getBillByID($id=0){
		$db = Register::get('db');
		return $db->get("SELECT * FROM ".DB_PREFIX."settings_merchants_result WHERE `id` = '".(int)$id."';");
	}
	
	function delete($id=0){
		$db = Register::get('db');
		$db->post("DELETE FROM ".DB_PREFIX."settings_merchants_result WHERE `id` = '".(int)$id."';");
	}
	
	private function updateBill($id,$status){
		$db = Register::get('db');
		$db->post("
			UPDATE ".DB_PREFIX."settings_merchants_result SET 
				`status` = '".$status."',
				`check_dt` = '".mktime()."'
			WHERE `merchant` = 'IPAY' AND `id` = '".(int)$id."';
		");
	}
}

?>