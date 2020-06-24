<?php

namespace SM\XRetail\Repositories;

use Exception;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;
use SM\Core\Api\Data\XProvince;
use SM\Core\Api\Data\XDistrict;
use SM\XRetail\Helper\DataConfig;
use SM\XRetail\Repositories\Contract\ServiceAbstract;
use SM\XRetail\Model\Province;
use SM\XRetail\Model\District;
use SM\XRetail\Model\ResourceModel\Province\CollectionFactory as ProvinceCollectionFactory;
use SM\XRetail\Model\ResourceModel\District\CollectionFactory as DistrictCollectionFactory;

class ProvincesDistrictsManagement extends ServiceAbstract
{
    /**
     * @var \SM\XRetail\Model\ResourceModel\Province\CollectionFactory
     */
    protected $provinceFactory;
    /**
     * @var \SM\XRetail\Model\ResourceModel\District\CollectionFactory
     */
    protected $districtFactory;

    /**
     * ProvincesDistrictsManagement constructor.
     *
     * @param \Magento\Framework\App\RequestInterface                    $requestInterface
     * @param \SM\XRetail\Helper\DataConfig                              $dataConfig
     * @param \Magento\Store\Model\StoreManagerInterface                 $storeManager
     * @param \SM\XRetail\Model\ResourceModel\Province\CollectionFactory $provinceFactory
     * @param \SM\XRetail\Model\ResourceModel\District\CollectionFactory $districtFactory
     */
    public function __construct(
        RequestInterface $requestInterface,
        DataConfig $dataConfig,
        StoreManagerInterface $storeManager,
        ProvinceCollectionFactory $provinceFactory,
        DistrictCollectionFactory $districtFactory
    ) {
        $this->provinceFactory = $provinceFactory;
        $this->districtFactory = $districtFactory;
        parent::__construct($requestInterface, $dataConfig, $storeManager);
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getProvincesDistrictsData() {
        $searchCriteria = $this->getSearchCriteria();
        $collection = $this->getProvinceCollection($searchCriteria);
        $items      = [];

        if ($collection->getLastPageNumber() >= $searchCriteria->getData('currentPage')) {
            foreach ($collection as $objectModel) {
                $province              = new XProvince();
                try {
                    $province['districts'] = $this->getDistrictsByProvince($searchCriteria, $objectModel);
                }
                catch (\ReflectionException $e) {
                }
                $items[]               = $province->addData($objectModel->getData());
            }
        }

        return $this->getSearchResult()
                    ->setItems($items)
                    ->setTotalCount($collection->getSize())
                    ->setLastPageNumber(1)
                    ->getOutput();
    }

    /**
     * @param                            $searchCriteria
     * @param \SM\XRetail\Model\Province $province
     *
     * @return array
     * @throws \ReflectionException
     */
    protected function getDistrictsByProvince($searchCriteria, Province $province)
    {
        $collection = $this->getDistrictCollection($searchCriteria, $province);

        $districts = [];
        foreach ($collection as $district) {
            $dis         = new XDistrict($district->getData());
            $districts[] = $dis->getOutput();
        }

        return $districts;
    }

    /**
     * @param                            $searchCriteria
     * @param \SM\XRetail\Model\Province $province
     *
     * @return mixed
     */
    protected function getDistrictCollection($searchCriteria, Province $province)
    {
        $collection = $this->getDistrictsFactory();

        if (is_nan($searchCriteria->getData('currentPage'))) {
            $collection->setCurPage(1);
        } else {
            $collection->setCurPage($searchCriteria->getData('currentPage'));
        }
        if (is_nan($searchCriteria->getData('pageSize'))) {
            $collection->setPageSize(
                DataConfig::PAGE_SIZE_LOAD_PROVINCE
            );
        } else {
            $collection->setPageSize(
                $searchCriteria->getData('pageSize')
            );
        }
        $collection->addFieldToFilter('province_id', $province->getId());

        return $collection;
    }


    /**
     * @param $searchCriteria
     *
     * @return mixed
     */
    protected function getProvinceCollection($searchCriteria)
    {
        $collection = $this->getProvincesFactory();

        if (is_nan($searchCriteria->getData('currentPage'))) {
            $collection->setCurPage(1);
        } else {
            $collection->setCurPage($searchCriteria->getData('currentPage'));
        }
        if (is_nan($searchCriteria->getData('pageSize'))) {
            $collection->setPageSize(
                DataConfig::PAGE_SIZE_LOAD_PROVINCE
            );
        } else {
            $collection->setPageSize(
                $searchCriteria->getData('pageSize')
            );
        }

        return $collection;
    }

    /**
     * @return mixed
     */
    protected function getProvincesFactory()
    {
        return $this->provinceFactory->create();
    }

    /**
     * @return mixed
     */
    protected function getDistrictsFactory()
    {
        return $this->districtFactory->create();
    }
}
