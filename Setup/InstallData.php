<?php

namespace Tutorial\Example\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $setup->getConnection()->insert(
            $setup->getTable('tutorial_example'),
            [
                'title' => 'How to create a simple module',
                'summary' => 'The summary',
                'description' => 'The description',
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 1
            ]
        );

        $setup->getConnection()->insert(
            $setup->getTable('tutorial_example'),
            [
                'title' => 'Create a module with custom database table',
                'summary' => 'The summary',
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 1
            ]
        );

        $setup->endSetup();
    }
}