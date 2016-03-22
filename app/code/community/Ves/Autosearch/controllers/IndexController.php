<?php
/******************************************************
 * @package Ves Autosearch module for Magento 1.4.x.x and Magento 1.9.x.x
 * @version 1.0.0.1
 * @author http://landofcoder.com
 * @copyright	Copyright (C) December 2010 LandOfCoder.com <@emai:landofcoder@gmail.com>.All rights reserved.
 * @license		GNU General Public License version 2
*******************************************************/
class Ves_Autosearch_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }

    public function ajaxgetproductAction()
    {
    	$json = array();

      $limit = Mage::getStoreConfig("ves_autosearch/ves_autosearch/limit");
      $thumbWidth = Mage::getStoreConfig("ves_autosearch/ves_autosearch/thumbWidth");
      $thumbHeight = Mage::getStoreConfig("ves_autosearch/ves_autosearch/thumbHeight");

		  $category_id = $this->getRequest()->getPost('filter_category_id'); // if you know static category then enter number

		  $searchstring = $this->getRequest()->getPost('filter_name');

      $collection = Mage::getResourceModel('catalogsearch/search_collection');
      $collection->addAttributeToSelect('*'); //add product attribute to be fetched
      $collection->addAttributeToFilter('status',1); //only enabled product

      $collection->addSearchFilter($searchstring)
              ->addStoreFilter()
              ->addMinimalPrice()
              ->addTaxPercents();

      Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
      Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($collection);

      if($category_id) {
        $collection->addCategoryFilter(Mage::getModel('catalog/category')->load($category_id)); //category filter
      }

      $collection->setPage(1, $limit);

        //---------------------------------
      $collection2 = Mage::getResourceModel('catalogsearch/search_collection');
      $collection2->addAttributeToSelect('*'); //add product attribute to be fetched
      $collection2->addAttributeToFilter('status',1); //only enabled product
      
      $collection2->addSearchFilter($searchstring)
              ->addStoreFilter()
              ->addMinimalPrice()
              ->addTaxPercents();

      Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection2);
      Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($collection2);

      if($category_id)
        $collection2->addCategoryFilter(Mage::getModel('catalog/category')->load($category_id)); //category filter

        $total = $collection2->Count();
        //===========================================
        if(!empty($collection))
        {
            foreach ($collection as $_product){

              $item_html = Mage::app()->getLayout()
                          ->createBlock("ves_autosearch/item")
                          ->assign("product", $_product)
                          ->toHtml();


              $json[] = array(
                  					'total' 		 => $total,
                  					'product_id' => $_product->getId(),
                  					'name'       => strip_tags(html_entity_decode($_product->getName(), ENT_QUOTES, 'UTF-8')),	
                  					'image'		 => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product/'.$_product->getData('small_image'),
                  					'link'		 => $_product->getProductUrl(),
                  					'price'      => $_product->getPrice(),
                            'html'     => $item_html
                  				);	       
            }

            if (!empty($json)) {
              $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($json));

            }else{
            	echo 'No products exists';
            }
           
        } else {
            echo 'No products exists';
        }              
 
    }

}