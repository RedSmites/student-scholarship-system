<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ScholarshipApplicationsExampleSeeder extends Seeder
{
    public function run(): void
    {
        $scholarship = $this->db->table('scholarships')
            ->select('id')
            ->orderBy('id', 'ASC')
            ->get()
            ->getRowArray();

        if (! $scholarship) {
            throw new \RuntimeException('Create a scholarship before seeding example applications.');
        }

        $examples = [
            [
                'first_name' => 'Alex',
                'middle_name' => 'Demo',
                'last_name' => 'Sample',
                'email' => 'alex.sample@example.test',
                'mobile_number' => '09000000001',
                'status' => 'pending',
                'parents_income' => '12000',
                'course' => 'Bachelor of Science in Information Technology',
            ],
            [
                'first_name' => 'Casey',
                'middle_name' => 'Example',
                'last_name' => 'Student',
                'email' => 'casey.student@example.test',
                'mobile_number' => '09000000002',
                'status' => 'under_review',
                'parents_income' => '15000',
                'course' => 'Bachelor of Secondary Education',
            ],
            [
                'first_name' => 'Jordan',
                'middle_name' => 'Demo',
                'last_name' => 'Applicant',
                'email' => 'jordan.applicant@example.test',
                'mobile_number' => '09000000003',
                'status' => 'approved',
                'parents_income' => '10000',
                'course' => 'Bachelor of Science in Agriculture',
            ],
        ];

        $now = date('Y-m-d H:i:s');

        foreach ($examples as $example) {
            $existing = $this->db->table('applications')
                ->select('id')
                ->where('email', $example['email'])
                ->get()
                ->getRowArray();

            if ($existing) {
                continue;
            }

            $details = [
                'first_name' => $example['first_name'],
                'middle_name' => $example['middle_name'],
                'last_name' => $example['last_name'],
                'date_of_birth' => '2005-04-12',
                'place_of_birth' => 'Demo City',
                'sex' => 'Female',
                'civil_status' => 'Single',
                'citizenship' => 'Filipino',
                'religion' => 'Not specified',
                'barangay' => 'Poblacion East (Urban)',
                'street' => 'Example Street',
                'house_number' => '1',
                'mobile_number' => $example['mobile_number'],
                'email' => $example['email'],
                'academic_level' => 'College Level',
                'school_name' => 'Example Community College',
                'school_street' => 'College Road',
                'school_barangay' => 'Poblacion East (Urban)',
                'school_municipality' => 'Demo Municipality',
                'school_province' => 'Demo Province',
                'school_type' => 'Public',
                'father_last_name' => 'Sample',
                'father_first_name' => 'Taylor',
                'father_occupation' => 'Example occupation',
                'father_education' => 'High school graduate',
                'father_street' => 'Example Street',
                'father_barangay' => 'Poblacion East (Urban)',
                'father_municipality' => 'Demo Municipality',
                'father_province' => 'Demo Province',
                'father_status' => 'Living',
                'mother_last_name' => 'Sample',
                'mother_first_name' => 'Morgan',
                'mother_occupation' => 'Example occupation',
                'mother_education' => 'High school graduate',
                'mother_street' => 'Example Street',
                'mother_barangay' => 'Poblacion East (Urban)',
                'mother_municipality' => 'Demo Municipality',
                'mother_province' => 'Demo Province',
                'mother_status' => 'Living',
                'parents_income' => $example['parents_income'],
                'course' => $example['course'],
                'declaration' => 'yes',
            ];

            $inserted = $this->db->table('applications')->insert([
                'user_id' => null,
                'scholarship_id' => (int) $scholarship['id'],
                'status' => $example['status'],
                'remarks' => null,
                'applicant_name' => $example['last_name'] . ', ' . $example['first_name'] . ' ' . $example['middle_name'],
                'email' => $example['email'],
                'mobile_number' => $example['mobile_number'],
                'application_data' => json_encode($details, JSON_THROW_ON_ERROR),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if (! $inserted) {
                throw new \RuntimeException('Could not create example application for ' . $example['email'] . '.');
            }
        }
    }
}
