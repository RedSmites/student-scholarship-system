<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddScholarshipIdToScholarshipApplications extends Migration
{
    public function up()
    {
        $this->forge->addColumn('scholarship_applications', [
            'scholarship_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'mobile_number'],
        ]);
        $this->forge->addForeignKey(
            'scholarship_id',
            'scholarships',
            'id',
            'CASCADE',
            'SET NULL',
            'fk_student_applications_scholarship'
        );
        $this->forge->processIndexes('scholarship_applications');
    }

    public function down()
    {
        $this->forge->dropForeignKey('scholarship_applications', 'fk_student_applications_scholarship');
        $this->forge->dropColumn('scholarship_applications', 'scholarship_id');
    }
}
