<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnablePublicApplicationsAndDocuments extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('applications', [
            'user_id' => ['name' => 'user_id', 'type' => 'INT', 'unsigned' => true, 'null' => true],
        ]);
        $this->forge->addColumn('applications', [
            'applicant_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'user_id'],
            'email' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true, 'after' => 'applicant_name'],
            'mobile_number' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true, 'after' => 'email'],
            'application_data' => ['type' => 'LONGTEXT', 'null' => true, 'after' => 'mobile_number'],
        ]);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'application_id' => ['type' => 'INT', 'unsigned' => true],
            'requirement_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'requirement_name_snapshot' => ['type' => 'VARCHAR', 'constraint' => 150],
            'file_category' => ['type' => 'VARCHAR', 'constraint' => 32],
            'file_path' => ['type' => 'VARCHAR', 'constraint' => 255],
            'original_filename' => ['type' => 'VARCHAR', 'constraint' => 255],
            'mime_type' => ['type' => 'VARCHAR', 'constraint' => 100],
            'file_size' => ['type' => 'INT', 'unsigned' => true],
            'admin_verified' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'admin_remarks' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('application_id');
        $this->forge->addKey('requirement_id');
        $this->forge->addForeignKey('application_id', 'applications', 'id', 'CASCADE', 'CASCADE', 'fk_application_documents_application');
        $this->forge->addForeignKey('requirement_id', 'scholarship_requirements', 'id', 'CASCADE', 'SET NULL', 'fk_application_documents_requirement');
        $this->forge->createTable('application_documents', true);
    }

    public function down()
    {
        $this->forge->dropTable('application_documents', true);
        $this->forge->dropColumn('applications', ['applicant_name', 'email', 'mobile_number', 'application_data']);
        $this->forge->modifyColumn('applications', [
            'user_id' => ['name' => 'user_id', 'type' => 'INT', 'unsigned' => true, 'null' => false],
        ]);
    }
}
