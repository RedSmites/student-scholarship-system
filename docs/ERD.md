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
        id PK
        first_name
        middle_name
        last_name
        suffix
        email
        password_hash
        role
        created_at
        updated_at
    }

    SCHOLARSHIPS {
        id PK
        title
        provider
        description
        eligibility
        benefits
        deadline
        status
    }

    SCHOLARSHIP_REQUIREMENTS {
        id PK
        scholarship_id FK
        requirement_name
        description
        is_required
        is_active
        created_at
        updated_at
    }

    APPLICATIONS {
        id PK
        user_id FK "nullable for public applications"
        scholarship_id FK
        applicant_name
        email
        mobile_number
        application_data
        status
        admin_remarks
        created_at
        updated_at
    }

    APPLICATION_DOCUMENTS {
        id PK
        application_id FK
        requirement_id FK "nullable"
        requirement_name_snapshot
        file_category
        file_path
        original_filename
        mime_type
        file_size
        admin_verified
        admin_remarks
        created_at
        updated_at
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
