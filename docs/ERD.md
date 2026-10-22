# Entity Relationship Diagram (ERD)

This ERD documents the existing Student Scholarship Information System database structure based on the repository's models and migrations. It is documentation only; it does **not** change the database schema.

## ERD

```mermaid
erDiagram
    USERS o|--o{ APPLICATIONS : submits
    SCHOLARSHIPS ||--o{ APPLICATIONS : receives
    SCHOLARSHIPS ||--o{ SCHOLARSHIP_REQUIREMENTS : defines
    APPLICATIONS ||--o{ APPLICATION_DOCUMENTS : includes
    SCHOLARSHIP_REQUIREMENTS o|--o{ APPLICATION_DOCUMENTS : identifies

    USERS {
        int id PK
        varchar first_name
        varchar middle_name
        varchar last_name
        varchar suffix
        varchar email
        varchar password_hash
        varchar role
        datetime created_at
        datetime updated_at
    }

    SCHOLARSHIPS {
        int id PK
        varchar title
        varchar provider
        text description
        text eligibility
        text benefits
        date deadline
        varchar status
    }

    SCHOLARSHIP_REQUIREMENTS {
        int id PK
        int scholarship_id FK
        varchar requirement_name
        text description
        boolean is_required
        boolean is_active
        datetime created_at
        datetime updated_at
    }

    APPLICATIONS {
        int id PK
        int user_id FK "nullable for public applications"
        int scholarship_id FK
        varchar applicant_name
        varchar email
        varchar mobile_number
        longtext application_data
        varchar status
        text admin_remarks
        datetime created_at
        datetime updated_at
    }

    APPLICATION_DOCUMENTS {
        int id PK
        int application_id FK
        int requirement_id FK "nullable"
        varchar requirement_name_snapshot
        varchar file_category
        varchar file_path
        varchar original_filename
        varchar mime_type
        int file_size
        boolean admin_verified
        text admin_remarks
        datetime created_at
        datetime updated_at
    }
```

## Relationship summary

- **Users → Applications:** A registered user may submit multiple applications. `applications.user_id` can be `NULL` for public applications.
- **Scholarships → Applications:** Each application is for one scholarship; a scholarship can receive many applications.
- **Scholarships → Scholarship Requirements:** A scholarship can define multiple requirements.
- **Applications → Application Documents:** An application can include multiple uploaded documents. Deleting an application cascades to its documents.
- **Scholarship Requirements → Application Documents:** A document may refer to a requirement. The requirement reference can be `NULL`; the document also stores a requirement-name snapshot.

## Notes

- The diagram is intended to describe the existing application, not propose a new schema.
- Some column types and foreign-key details should be verified against the actual `scholarship_system` database export before using this as a final database specification.
- The separate `scholarship_applications` table appears in a migration as an alternate/legacy-style application table. The current application controllers use `applications`, so it is not included in the main ERD.
