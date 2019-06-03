<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_ProductNotification
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\RequestForQuote\Setup;

use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;

class InstallData implements InstallDataInterface
{
	/**
	 * @param GroupFactory $groupFactory 
	 */
	public function __construct(
		EavSetupFactory $eavSetupFactory
	) {
		$this->eavSetupFactory = $eavSetupFactory;
	}

	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
 		$data = array(
				'group'                         => 'General',
				'type'                          => 'int',
				'input'                         => 'boolean',
				'default'                       => 1,
				'label'                         => 'Product Quote',
				'backend'                       => '',
				'frontend'                      => '',
				'source'                        => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
				'visible'                       => 1,
				'required'                      => 0,
				'user_defined'                  => 1,
				'used_for_price_rules'          => 1,
				'position'                      => 2,
				'unique'                        => 0,
				'default'                       => '',
				'sort_order'                    => 100,
				'is_global'                     => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
				'is_required'                   => 0,
				'is_configurable'               => 1,
				'is_searchable'                 => 0,
				'is_visible_in_advanced_search' => 0,
				'is_comparable'                 => 0,
				'is_filterable'                 => 0,
				'is_filterable_in_search'       => 1,
				'is_used_for_promo_rules'       => 1,
				'is_html_allowed_on_front'      => 0,
				'is_visible_on_front'           => 1,
				'used_in_product_listing'       => 1,
				'used_for_sort_by'              => 0,
 			);
 		$eavSetup->addAttribute(
 			\Magento\Catalog\Model\Product::ENTITY,
 			'product_quote',
 			$data);
	}
}
