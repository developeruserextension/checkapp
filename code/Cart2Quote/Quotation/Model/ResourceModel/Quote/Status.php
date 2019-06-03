<?php
/**
 *  CART2QUOTE CONFIDENTIAL
 *  __________________
 *  [2009] - [2018] Cart2Quote B.V.
 *  All Rights Reserved.
 *  NOTICE OF LICENSE
 *  All information contained herein is, and remains
 *  the property of Cart2Quote B.V. and its suppliers,
 *  if any.  The intellectual and technical concepts contained
 *  herein are proprietary to Cart2Quote B.V.
 *  and its suppliers and may be covered by European and Foreign Patents,
 *  patents in process, and are protected by trade secret or copyright law.
 *  Dissemination of this information or reproduction of this material
 *  is strictly forbidden unless prior written permission is obtained
 *  from Cart2Quote B.V.
 * @category    Cart2Quote
 * @package     Quotation
 * @copyright   Copyright (c) 2018. Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\Quotation\Model\ResourceModel\Quote;

/**
 * Quote status resourcemodel
 */
class Status extends \Magento\Sales\Model\ResourceModel\Order\Status
{
    /**
     * Status labels table
     * @var string
     */
    protected $labelsTable;

    /**
     * Status state table
     * @var string
     */
    protected $stateTable;

    /**
     * Check is this status used in quotes
     * @param string $status
     * @return bool
     */
    public function checkIsStatusUsed($status)
    {
        return (bool)$this->getConnection()->fetchOne(
            $this->getConnection()->select()
                ->from(['sfo' => $this->getTable('quotation_quote')], [])
                ->where('status = ?', $status)
                ->limit(1)
                ->columns([new \Zend_Db_Expr(1)])
        );
    }

    /**
     * Internal constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_init('quotation_quote_status', 'status');
        $this->_isPkAutoIncrement = false;
        $this->labelsTable = $this->getTable('quotation_quote_status_label');
        $this->stateTable = $this->getTable('quotation_quote_status_state');
    }
}