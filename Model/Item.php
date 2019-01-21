<?php

namespace Tutorial\Example\Model;

use Magento\Framework\Model\AbstractModel;

class Item extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Tutorial\Example\Model\ResourceModel\Item::class);
    }
}