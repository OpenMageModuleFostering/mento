<?php
class Mento_Social_Model_Store_Api extends Mage_Core_Model_Store_Api {



    public function getweb()
    {
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
        

        return $stores;
    }

    public function items()
    {
        // Retrieve stores
        $stores = Mage::app()->getStores();

        // Make result array
        $result = array();
        foreach ($stores as $store) {
            $result[] = array(
                'store_id'    => $store->getId(),
                'code'        => $store->getCode(),
                'jimit'        => "jimit",
                'website_id'  => $store->getWebsiteId(),
                'group_id'    => $store->getGroupId(),
                'name'        => $store->getName(),
                'sort_order'  => $store->getSortOrder(),
                'is_active'   => $store->getIsActive()
            );
        }

        return $result;
    }
    /*
    public function info($productId, $store = null, $attributes = null, $identifierType = null){
        $product = $this->_getProduct($productId, $store, $identifierType);	
        $result = parent::info($productId, $store = null, $attributes = null, $identifierType = null);
        //add a new element in here called product_url. You can even wrap it in some condition to see if the product is assigned to the current website or if the product is enabled or visible. ($product->getWebsiteIds(), $product->getStatus(), $product->getVisibility()). 

        $result['product_url'] = $product->getUrlInStore($store);


		$gallery = $product->getTypeInstance(true)->getSetAttributes($product);
        

        $galleryData = $product->getData('media_gallery');
		
		$images	=	array();
        if (!isset($galleryData['images']) || !is_array($galleryData['images'])) {
            $images	=	array();
        }

        foreach ($galleryData['images'] as &$image) {
			$images[] 	= array(
				            'file'      => $image['file'],
				            'label'     => $image['label'],
				            'position'  => $image['position'],
				            'url'       => Mage::getSingleton('catalog/product_media_config')->getMediaUrl($image['file'])
				        );

        }

		$result['gallery'] 	=	$images;
        return $result;
    }
    */

}
