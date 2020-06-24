<?php
namespace SM\XRetail\Model\ResourceModel\Province;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'SM\XRetail\Model\Province',
            'SM\XRetail\Model\ResourceModel\Province'
        );
    }
}
