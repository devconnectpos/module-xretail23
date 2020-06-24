<?php


namespace SM\XRetail\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use SM\XRetail\Data\ProvincesData;
use SM\XRetail\Model\Province;
use SM\XRetail\Model\ProvinceFactory;
use SM\XRetail\Model\DistrictFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \SM\XRetail\Model\ProvinceFactory
     */
    protected $provinceFactory;
    /**
     * @var \SM\XRetail\Model\DistrictFactory
     */
    protected $districtFactory;

    /**
     * UpgradeData constructor.
     *
     * @param \SM\XRetail\Model\ProvinceFactory $provinceFactory
     * @param \SM\XRetail\Model\DistrictFactory $districtFactory
     */
    public function __construct(
        ProvinceFactory $provinceFactory,
        DistrictFactory $districtFactory
    ) {
        $this->provinceFactory = $provinceFactory;
        $this->districtFactory = $districtFactory;
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface   $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		if (version_compare($context->getVersion(), '0.4.1', '<')) {
			$this->assignProvincesData();
		}
		
		$installer->endSetup();
	}

	protected function assignProvincesData()
    {
        $provincesDistrictsData = ProvincesData::getAllProvincesData();

        foreach ($provincesDistrictsData as $key => $value) {
            $province = $this->getProvincesCollection();

            $province->setData('name', $value['name']);
            $province->setData('value', $key);

            $province->save();

            $this->assignDistrictsData($province, $value['cities']);
        }
    }

    /**
     * @param \SM\XRetail\Model\Province $province
     * @param array                      $cities
     */
    protected function assignDistrictsData(Province $province, $cities = [])
    {
        $provinceId = $province->getId();

        if (!$provinceId) {
            return;
        }
        foreach ($cities as $key => $value) {
            $district = $this->getDistrictsCollection();

            $district->setData('name', $value);
            $district->setData('value', $key);
            $district->setData('province_id', $provinceId);

            $district->save();
        }
    }

    /**
     * @return mixed
     */
    protected function getProvincesCollection()
    {
        return $this->provinceFactory->create();
    }

    /**
     * @return mixed
     */
    protected function getDistrictsCollection()
    {
        return $this->districtFactory->create();
    }
}
