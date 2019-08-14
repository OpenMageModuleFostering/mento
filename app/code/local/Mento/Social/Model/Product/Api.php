<?php
class Mento_Social_Model_Product_Api extends Mage_Catalog_Model_Product_Api {


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


}
