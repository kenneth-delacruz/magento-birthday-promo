<?php
namespace Kdc\BirthdayPromo\Setup\Patch\Data;

use Magento\SalesRule\Model\RuleFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\SalesRule\Model\ResourceModel\Rule as RuleResource;
use Magento\Framework\App\State;
use Magento\Framework\Serialize\Serializer\Json;

class AddBirthdayDiscountRule implements DataPatchInterface
{
    private $moduleDataSetup;
    private $ruleFactory;
    private $ruleResource;
    private $state;
    private $jsonSerializer;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        RuleFactory $ruleFactory,
        RuleResource $ruleResource,
        State $state,
        Json $jsonSerializer
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->ruleFactory = $ruleFactory;
        $this->ruleResource = $ruleResource;
        $this->state = $state;
        $this->jsonSerializer = $jsonSerializer;
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $this->state->setAreaCode('adminhtml');

        // Define the correct condition format
        $conditions = [
            "type" => "Magento\\SalesRule\\Model\\Rule\\Condition\\Combine",
            "attribute" => null,
            "operator" => null,
            "value" => "1",
            "is_value_processed" => null,
            "aggregator" => "all", // All conditions must be true
            "conditions" => [
                [
                    "type" => "Kdc\\BirthdayPromo\\Model\\Rule\\Condition\\BirthdayCondition",
                    "attribute" => "birthday_promo",
                    "operator" => "==",
                    "value" => "today",
                    "is_value_processed" => false
                ]
            ]
        ];

        /** @var \Magento\SalesRule\Model\Rule $rule */
        $rule = $this->ruleFactory->create();
        $rule->setName('Birthday 50% Off')
            ->setDescription('50% discount for customers on their birthday')
            ->setIsActive(1)
            ->setWebsiteIds([1]) // Adjust if multiple websites
            ->setCustomerGroupIds([1, 2, 3]) // Apply to all customer groups
            ->setFromDate(null)
            ->setToDate(null)
            ->setUsesPerCustomer(1)
            ->setIsAdvanced(1)
            ->setStopRulesProcessing(0)
            ->setDiscountAmount(50)
            ->setDiscountQty(null)
            ->setDiscountStep(0)
            ->setSimpleAction('by_percent') // 50% off
            ->setApplyToShipping(0)
            ->setTimesUsed(0)
            ->setIsRss(0)
            ->setCouponType(1) // No coupon required
            ->setConditionsSerialized($this->jsonSerializer->serialize($conditions)) // Apply BirthdayCondition
            ->setActionsSerialized('');

        $this->ruleResource->save($rule);

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
