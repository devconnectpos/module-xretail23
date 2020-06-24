<?php
namespace SM\XRetail\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Province extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'sm_xretail_provinces';

    protected function _construct()
    {
        $this->_init(\SM\XRetail\Model\ResourceModel\Province::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
