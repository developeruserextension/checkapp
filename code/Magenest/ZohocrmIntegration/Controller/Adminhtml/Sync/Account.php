<?php
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Sync;

use Magenest\ZohocrmIntegration\Model\Sync;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

/**
 * Class Account
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\Sync
 */
class Account extends Action
{
    /**
     * @var Sync\Account
     */
    protected $_account;

    /**
     * Customer constructor.
     * @param Context $context
     * @param Sync\Account $account
     */
    public function __construct(
        Context $context,
        Sync\Account $account
    ) {
        $this->_account = $account;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        try {

            $response = $this->_account->syncAllQueue();
            $this->messageManager->addSuccess(
                __('Account is synced successfully')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('Something happen during syncing process. Detail: ' . $e->getMessage() . '. Response Log: '.serialize($response))
            );
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_ZohocrmIntegration::config_zohocrm');
    }
}
