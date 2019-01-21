<?php

namespace Tutorial\Example\Block;

use Magento\Framework\View\Element\Template;
use Tutorial\Example\Model\ResourceModel\Item\Collection;
use Tutorial\Example\Model\ResourceModel\Item\CollectionFactory;

class Index extends Template
{
    private $collectionFactory;

    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return \Tutorial\Example\Model\Item[]
     */
    public function getItems()
    {
        return $this->collectionFactory->create()->getItems();
    }
}