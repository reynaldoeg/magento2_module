<?php

namespace Tutorial\Example\Model\ResourceModel\Item;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Tutorial\Example\Model\Item;
use Tutorial\Example\Model\ResourceModel\Item as ItemResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(Item::class, ItemResource::class);
    }
}
