<?php
namespace Sanket\Test\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $tableName = $installer->getTable('test_details');

        if (!$installer->tableExists('test_details')) {
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'test_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Image ID'
                ) ->addColumn(
                    'test_name',
                    Table::TYPE_TEXT,
                    255,
                    array(
                        'nullable'  => false,
                    ),
                    'Test Name'
                ) ->addColumn(
                    'test_no',
                    Table::TYPE_INTEGER,
                    12,
                    array(
                        'nullable'  => false,
                    ),
                    'Test Number'
                )
                ->addColumn(
                    'image',
                    Table::TYPE_TEXT,
                    255,
                    array(
                        'nullable'  => false,
                    ),
                    'Image'
                );
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
