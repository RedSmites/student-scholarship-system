<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateScholarshipApplications extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'applicant_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'email' => ['type' => 'VARCHAR', 'constraint' => 190],
            'mobile_number' => ['type' => 'VARCHAR', 'constraint' => 30],
            'application_data' => ['type' => 'LONGTEXT'],
            'photo_path' => ['type' => 'VARCHAR', 'constraint' => 255],
            'documents' => ['type' => 'LONGTEXT'],
            'signature_path' => ['type' => 'VARCHAR', 'constraint' => 255],
            'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'pending'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('email');
        $this->forge->createTable('scholarship_applications', true);
    }

    public function down()
    {
        $this->forge->dropTable('scholarship_applications', true);
    }
}
