<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUniqueNameToProductCategories extends Migration
{
    protected string $tableName = 'product_category';

    public function up()
    {
        $this->forge->addUniqueKey('name');
        $this->forge->processIndexes($this->tableName);
    }

    public function down()
    {
        $this->forge->dropKey($this->tableName, 'name');
    }
}
