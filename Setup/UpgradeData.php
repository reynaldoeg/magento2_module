<?php

namespace Tutorial\Example\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            // Get tutorial_example table
            $tableName = $setup->getTable('tutorial_example');

            // Update Row
            $setup->getConnection()->update(
                $setup->getTable($tableName),
                [
                    'description' => 'New description'
                ],
                $setup->getConnection()->quoteInto('id = ?', 2)
            );

            // Add new row
            $data = [
                [
                    'title' => 'How to create another module',
                    'summary' => 'The new summary',
                    'description' => 'The description',
                    'created_at' => date('Y-m-d H:i:s'),
                    'status' => 1
                ],
                [
                    'title' => 'Create another module with custom database table',
                    'summary' => 'The other summary',
                    'description' => 'The description',
                    'created_at' => date('Y-m-d H:i:s'),
                    'status' => 1
                ]
            ];

            // Insert data to table
            foreach ($data as $item) {
                $setup->getConnection()->insert($tableName, $item);
            }
        }

        $setup->endSetup();
    }
}