<?php

namespace Tutorial\Example\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            // Get tutorial_example table
            $tableName = $setup->getTable('tutorial_example');

            $setup->getConnection()->addColumn(
                $setup->getTable($tableName),
                'newcolumn',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'New Column'
                ]
            );
        }

        $setup->endSetup();
    }
}