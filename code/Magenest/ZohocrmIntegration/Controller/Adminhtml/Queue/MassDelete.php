<?php
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Queue;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\CollectionFactory;
use Magenest\ZohocrmIntegration\Model\QueueFactory;
use Magento\Framework\Controller\ResultFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var QueueFactory
     */
    protected $queueFactory;
    protected $_resource;
    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param QueueFactory $queueFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        QueueFactory $queueFactory,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->queueFactory = $queueFactory;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_resource = $resource;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();
            $connection = $this->_resource->getConnection();
            $connection->delete(
                'magenest_zohocrm_queue',
                ['id IN (?)' => $collection->getAllIds()]);
            $this->messageManager->addSuccess(__('Total of %1 record(s) were deleted.'), $collectionSize);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_ZohocrmIntegration::queue');
    }
}
