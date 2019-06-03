<?php
/**
 *  CART2QUOTE CONFIDENTIAL
 *  __________________
 *
 *  [2009] - [2018] Cart2Quote B.V.
 *  All Rights Reserved.
 *
 *  NOTICE OF LICENSE
 *
 *  All information contained herein is, and remains
 *  the property of Cart2Quote B.V. and its suppliers,
 *  if any.  The intellectual and technical concepts contained
 *  herein are proprietary to Cart2Quote B.V.
 *  and its suppliers and may be covered by European and Foreign Patents,
 *  patents in process, and are protected by trade secret or copyright law.
 *  Dissemination of this information or reproduction of this material
 *  is strictly forbidden unless prior written permission is obtained
 *  from Cart2Quote B.V.
 *
 * @category    Cart2Quote
 * @package     Quotation
 * @copyright   Copyright (c) 2018. Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\Quotation\Controller\Quote\Checkout;

/**
 * Class MoreOptions
 * @package Cart2Quote\Quotation\Controller\Quote\Checkout
 */
class MoreOptions extends DefaultCheckout
{
    /**
     * Redirect to customer checkout page if the quotation customer
     * is the same customer as logged in
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $this->initQuote();
        if ($this->isAutoLogin() && $this->hasValidHash()) {
            $this->autoLogin();
        }
        $url = $this->_url->getUrl('quotation/quote/view', ['quote_id' => $this->quote->getId()]);

        return $this->resultRedirectFactory->create()->setUrl($url);
    }
}
