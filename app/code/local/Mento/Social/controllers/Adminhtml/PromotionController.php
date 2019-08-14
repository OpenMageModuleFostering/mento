<?php
class Mento_Social_Adminhtml_PromotionController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {

       	$this->loadLayout()
			->_setActiveMenu('social');

	   	$this->_title($this->__("Mento"));

		//$fileContent	=	"Test";
		//$block = $this->getLayout()->createBlock('core/text', 'socialpilot-block')->setText($fileContent);
        //$this->_addContent($block);

	   $this->renderLayout();
    }
	
	public function apiroleAction() {
		
		$theRoleExist	=	Mage::helper('social')->checkRole();
		if(!$theRoleExist)
		{
			$role = Mage::getSingleton('api/roles')->setName('Mento')->setPid(false)->setRoleType('G')->save();
			Mage::getSingleton("api/rules")->setRoleId($role->getId())->setResources(array('all'))->saveRel();
		}

		$objApiRole = Mage::getSingleton('api/roles');
		$roleNames = $objApiRole->getCollection();
		$roleNames->getSelect()->where('role_name=?','Mento');
		$api_role_data = $roleNames->getData();
	
	
		Mage::helper('social')->deleteUser();

		$apiKey = uniqid();
		$user = Mage::getSingleton('api/user');
		$user->setData(array(
			'username' => 'Mento_Api_User', 'firstname' => 'Mento', 'lastname' => 'Module', 'email' => 'support@mento.io',
			'api_key' => $apiKey, 'api_key_confirmation' => $apiKey, 'is_active' => 1, 'user_roles' => '', 'assigned_user_role' => '',
			'role_name' => '', 'roles' => array($api_role_data[0]['role_id'])
		));
		$user->save()->load($user->getId());

		$user->setRoleIds(array($api_role_data[0]['role_id']))
			->setRoleUserId($user->getUserId())
			->saveRelations();
		
		
		$adminUser 		= Mage::getSingleton('admin/session'); 
		$userEmail 		= $adminUser->getUser()->getEmail();
		$userFirstname 	= $adminUser->getUser()->getFirstname();
		$userLastname 	= $adminUser->getUser()->getLastname();
		
		$full_name 		= $userFirstname.' '.$userLastname;
		$email 			= $userEmail;
		$api_user 		= 'Mento_Api_User';
		$api_key 		= $apiKey;

		$_websites = Mage::app()->getWebsites(); 
		$stores = array();
		$i = 0;
		foreach($_websites as $website)
		{     
			$stores[$i]["name"]    = $website->getName();
		 	$stores[$i]["root_cateogory"] = $website->getDefaultGroup()->getDefaultStore()->getRootCategoryId();
		 	$stores[$i]["store_code"] = $website->getDefaultGroup()->getDefaultStore()->getCode();
		 	$stores[$i]["base_url"]   = Mage::app()->getStore($website->getDefaultGroup()->getDefaultStore()->getId())->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		 	$stores[$i]["base_currency"] = $website->getDefaultGroup()->getDefaultStore()->getDefaultCurrency()->getCode();
			$i++;
		}

		
		$ch 	= 	curl_init();
		$url	=	"https://staging.mento.io/ecommerce/magento/apicreate";
		$req 	= 	array("full_name"=>$full_name,"email"=>$email,"api_user"=>$api_user,"api_key"=>$api_key, "stores"=>$stores);
		$str = http_build_query($req);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => TRUE));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$responce = curl_exec($ch);

		curl_close($ch);

		$json = json_decode($responce,true);


		if( $json['error'] == 0 )
		{
			$resource = Mage::getSingleton('core/resource');
			$writeConnection = $resource->getConnection('core_write');
			$query = "UPDATE mento SET mento_value = '". $json['data']['access_token']."' WHERE mento_id = 1 ";
			$writeConnection->query($query);
	
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('API user and role are successfully created'));
		} else {
			Mage::getSingleton('adminhtml/session')->addError($this->__($json )); //$json['data']['msg']
		}

		
        $this->_redirect('*/*/');
		
	}
}