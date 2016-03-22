<?php
/*------------------------------------------------------------------------
 # VenusTheme Pagebuilder Module 
 # ------------------------------------------------------------------------
 # author:    VenusTheme.Com
 # copyright: Copyright (C) 2012 http://www.venustheme.com. All Rights Reserved.
 # @license: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Websites: http://www.venustheme.com
 # Technical Support:  http://www.venustheme.com/
-------------------------------------------------------------------------*/
//require_once(Mage::getBaseDir('code').'/community/Ves/Pagebuider/Helper/widgetbase.php');
class Ves_Autosearch_Block_Autosearch extends Mage_Core_Block_Template 
{	
	var $_config = array();
	/**
	 * Contructor
	 */
	public function __construct($attributes = array())
	{	
		/*End init meida files*/
		if( !$this->getConfig('show') ) return;

		$this->_controller = 'autosearch';
		parent::__construct();		
	}
	public function listProductLink(){
    	return $this->getUrl('vesautosearch/index/ajaxgetproduct');
  	}
	/**
     * Rendering block content
     *
     * @return string
     */
	function _toHtml() 
	{	

		if( !$this->getConfig('show') ) return;

		$categories = "";

		if($this->getConfig('show_filter_category')) {

			$rootCatId = Mage::app()->getStore()->getRootCategoryId();
			$categories = $this->getTreeCategories($rootCatId, 0);

			
		}

		$this->assign( "categories", $categories );
		$this->assign( "listProductLink", $this->listProductLink() );
		$this->assign( "prefix", $this->getConfig('prefix') );
		$this->assign( "show_filter_category", $this->getConfig('show_filter_category') );
		$this->assign( "show_image", $this->getConfig('show_image') );
		$this->assign( "show_price", $this->getConfig('show_price') );

		$my_template = $this->getTemplate();
		if(empty($my_template)) {
			$template = 'ves/autosearch/default.phtml';
			if( $this->getConfig( "template" ) ){
				$template = $this->getConfig( "template" );
			}
			$this->setTemplate( $template );
		}

        return parent::_toHtml();
	}

	/**
	 * get value of the extension's configuration
	 *
	 * @return string
	 */
	function getConfig( $key, $panel='ves_autosearch' ){
		if(isset($this->_config[$key])) {
			return $this->_config[$key];
		} else {
			return Mage::getStoreConfig("ves_autosearch/$panel/$key");
		}
	}

	/**
	 * overrde the value of the extension's configuration
	 *
	 * @return string
	 */
	function setConfig( $key, $value ){
		$this->_config[$key] = $value;
		return $this;
	}

	public function getCatalogSearchLink() {
		return $this->getUrl('catalogsearch/result/');
	}

	public function getTreeCategories($parentId, $level = 0, $caret = '&nbsp;&nbsp;'){
		$category_id = $this->getRequest()->getParam("cat");

	    $allCats = Mage::getModel('catalog/category')->getCollection()
	                ->addAttributeToSelect('*')
	                ->addAttributeToFilter('is_active','1')
	                ->addAttributeToFilter('include_in_menu','1')
	                ->addAttributeToFilter('parent_id',array('eq' => $parentId));
	    $html = '';
	    $prefix = '';
	    //$children = Mage::getModel('catalog/category')->getCategories(7);
	    if($level) {
	    	for($i=0;$i < $level; $i++) {
	    		$prefix .= $caret;
	    	}
	    }
	    foreach ($allCats as $category) 
	    {

	    	$html .= '<option value="'.$category->getId().'" '.($category_id == $category->getId() ? 'selected="selected"':'') .'>'.$prefix.$category->getName().'</option>';
	        $subcats = $category->getChildren();
	        if($subcats != ''){
	            $html .= $this->getTreeCategories($category->getId(), (int)$level + 1, $caret.'&nbsp;');
	        }

	    }
	    return $html;
	}
}