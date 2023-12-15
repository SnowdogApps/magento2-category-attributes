<?php

declare(strict_types=1);

namespace Snowdog\CategoryAttributes\Setup;

use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    const CATEGORY_TEXT_BOTTOM_ATTR_CODE = 'category_text_bottom';
    const DEFAULT_ATTRIBUTE_GROUP_NAME = 'General Information';

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        $this->addCategoryTextBottomAttr($setup);
        $setup->endSetup();
    }

    private function addCategoryTextBottomAttr(ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            Category::ENTITY,
            self::CATEGORY_TEXT_BOTTOM_ATTR_CODE,
            [
                'type' => 'text',
                'label' => 'Category Bottom Text',
                'input' => 'textarea',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'wysiwyg_enabled' => true,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => self::DEFAULT_ATTRIBUTE_GROUP_NAME,
                'is_html_allowed_on_front' => true,
            ]
        );
    }
}
