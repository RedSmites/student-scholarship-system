-- DrawDB import file for RedSmites/student-scholarship-system
-- Documentation/diagram only. Do not run against your production database.
-- Based on docs/ERD.md and current application migrations; verify types against live DB.

CREATE TABLE users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  first_name VARCHAR(100) NOT NULL,
  middle_name VARCHAR(100) NULL,
  last_name VARCHAR(100) NOT NULL,
  suffix VARCHAR(20) NULL,
  email VARCHAR(190) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(30) NOT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE scholarships (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  provider VARCHAR(255) NULL,
  description TEXT NULL,
  eligibility TEXT NULL,
  benefits TEXT NULL,
  deadline DATE NULL,
  status VARCHAR(30) NOT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE scholarship_requirements (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  scholarship_id INT UNSIGNED NOT NULL,
  requirement_name VARCHAR(150) NOT NULL,
  description TEXT NULL,
  is_required TINYINT(1) NOT NULL DEFAULT 1,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_scholarship_requirements_scholarship_id (scholarship_id),
  CONSTRAINT fk_scholarship_requirements_scholarship
    FOREIGN KEY (scholarship_id) REFERENCES scholarships(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE applications (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NULL,
  applicant_name VARCHAR(255) NULL,
  email VARCHAR(190) NULL,
  mobile_number VARCHAR(30) NULL,
  application_data LONGTEXT NULL,
  scholarship_id INT UNSIGNED NOT NULL,
  status VARCHAR(30) NOT NULL,
  admin_remarks TEXT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_applications_user_id (user_id),
  KEY idx_applications_scholarship_id (scholarship_id),
  CONSTRAINT fk_applications_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_applications_scholarship
    FOREIGN KEY (scholarship_id) REFERENCES scholarships(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE application_documents (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  application_id INT UNSIGNED NOT NULL,
  requirement_id INT UNSIGNED NULL,
  requirement_name_snapshot VARCHAR(150) NOT NULL,
  file_category VARCHAR(32) NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  original_filename VARCHAR(255) NOT NULL,
  mime_type VARCHAR(100) NOT NULL,
  file_size INT UNSIGNED NOT NULL,
  admin_verified TINYINT(1) NOT NULL DEFAULT 0,
  admin_remarks TEXT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_application_documents_application_id (application_id),
  KEY idx_application_documents_requirement_id (requirement_id),
  CONSTRAINT fk_application_documents_application
    FOREIGN KEY (application_id) REFERENCES applications(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_application_documents_requirement
    FOREIGN KEY (requirement_id) REFERENCES scholarship_requirements(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
