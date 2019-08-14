<?php
class Mento_Social_Helper_Data extends Mage_Core_Helper_Abstract
{	
	
	public function checkRole() {
		$objApiRole = Mage::getSingleton('api/roles');
		$roleNames = $objApiRole->getCollection();
		$roleNames->getSelect()->where('role_name=?','Mento');
		$api_role_data = $roleNames->getData();
		$theRoleExist = false;
		if(!empty($api_role_data))
		{
			$theRoleExist = true;
		}
		
		return $theRoleExist;
	}

	

	public function checkUser() {
		$user = Mage::getSingleton('api/user');
		$apiUsernames = $user->getCollection();
		$apiUsernames->getSelect()->where('username=?','Mento_Api_User');
		$api_user_data = $apiUsernames->getData();
		
		$theUserExist = false;
		if(!empty($api_user_data))
		{
			$theUserExist = true;
		}
		
		return $theUserExist;
	}

	public function deleteUser() {
		$user = Mage::getSingleton('api/user');
		$apiUsernames = $user->getCollection();
		$apiUsernames->getSelect()->where('username=?','Mento_Api_User');
		$api_user_data = $apiUsernames->getData();
		
		if(!empty($api_user_data))
		{
			$user->setId($api_user_data[0]['user_id'])->delete();
		}
		
	}
	
	public function validAPI() {
		

		$theRoleExist	=	$this->checkRole();

		$theUserExist	=	$this->checkUser();
		
		if ( $theRoleExist and $theUserExist) {
			return true;
		} else {
			return false;
		}
	}

	public function validtoken() {
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$query = "SELECT mento_value FROM mento WHERE mento_id = '1' ";
		$token = $readConnection->fetchOne($query);
		
		if(strlen($token)==0) {
			return false;
		} else {
			$ch 	= 	curl_init();
			$url	=	"https://staging.mento.io/oauth/ping?access_token=".$token;
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => TRUE));
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$responce = curl_exec($ch);
			curl_close($ch);
	
			$json = json_decode($responce,true);
	
			if( $json['error'] == 1 )
			{
				return false;
			} else {
				return true;
			}
		}
	}

	public function gettoken() {
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$query = "SELECT mento_value FROM mento WHERE mento_id = '1' ";
		$token = $readConnection->fetchOne($query);

		return $token;
	}
}
	 