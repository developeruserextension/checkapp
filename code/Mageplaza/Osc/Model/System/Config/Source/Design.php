<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @copyright   Copyright (c) 2016 Mageplaza (http://mageplaza.com/)
 * @license     http://mageplaza.com/license-agreement.html
 */
namespace Mageplaza\Osc\Model\System\Config\Source;

/**
 * Class Design
 * @package Mageplaza\Osc\Model\System\Config\Source
 */
class Design
{
    const DESIGN_DEFAULT    = 'default';
    const DESIGN_FLAT       = 'flat';
    const DESIGN_MATERIAL   = 'material';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Default'),
                'value' => self::DESIGN_DEFAULT
            ],
            [
                'label' => __('Flat'),
                'value' => self::DESIGN_FLAT
            ],
			[
				'label' => __('Material'),
				'value' => self::DESIGN_MATERIAL
			]
        ];

        return $options;
    }
}
