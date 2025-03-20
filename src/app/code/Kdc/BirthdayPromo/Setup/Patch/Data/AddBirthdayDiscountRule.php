<?php

declare(strict_types=1);

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

    /**
     * Constructor for initializing rule-related dependencies.
     *
     * Injects necessary dependencies for setting up module data, handling promotional rules, 
     * managing application state, and serializing JSON data.
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup Handles setup and installation of module data.
     * @param \Magento\SalesRule\Model\RuleFactory $ruleFactory Factory for creating sales rule instances.
     * @param \Magento\SalesRule\Model\ResourceModel\Rule $ruleResource Manages database interactions for sales rules.
     * @param \Magento\Framework\App\State $state Provides application state management.
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer Handles JSON serialization and deserialization.
     */
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

    /**
     * Applies the Birthday Discount rule.
     *
     * This method creates and saves a new sales rule that grants a 50% discount 
     * to customers on their birthday. The rule applies automatically without a coupon.
     *
     * Steps:
     * - Starts the database setup process.
     * - Sets the area code to 'adminhtml' to avoid errors in CLI execution.
     * - Defines the rule conditions, specifically checking if today is the customer's birthday.
     * - Creates a new sales rule with predefined settings, such as a 50% discount.
     * - Saves the rule in the database.
     * - Ends the database setup process.
     *
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException If the rule already exists.
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $this->state->setAreaCode('adminhtml');

        $conditions = [
            "type" => "Magento\\SalesRule\\Model\\Rule\\Condition\\Combine",
            "attribute" => null,
            "operator" => null,
            "value" => "1",
            "is_value_processed" => null,
            "aggregator" => "all",
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
            ->setWebsiteIds([1])
            ->setCustomerGroupIds([1, 2, 3])
            ->setFromDate(null)
            ->setToDate(null)
            ->setUsesPerCustomer(1)
            ->setIsAdvanced(1)
            ->setStopRulesProcessing(0)
            ->setDiscountAmount(50)
            ->setDiscountQty(null)
            ->setDiscountStep(0)
            ->setSimpleAction('by_percent')
            ->setApplyToShipping(0)
            ->setTimesUsed(0)
            ->setIsRss(0)
            ->setCouponType(1)
            ->setConditionsSerialized($this->jsonSerializer->serialize($conditions))
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
