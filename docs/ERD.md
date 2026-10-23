# Student Scholarship Information System — ERD

This ERD shows primary keys (PK), foreign keys (FK), and verb-based relationships. **Data types are intentionally omitted from the diagram.** It documents the system and does not change the database schema.

```mermaid
erDiagram
    USERS o|--o{ APPLICATIONS : "submits"
    SCHOLARSHIPS ||--o{ APPLICATIONS : "receives"
    SCHOLARSHIPS ||--o{ SCHOLARSHIP_REQUIREMENTS : "defines"
    APPLICATIONS ||--o{ APPLICATION_DOCUMENTS : "includes"
    SCHOLARSHIP_REQUIREMENTS o|--o{ APPLICATION_DOCUMENTS : "is referenced by"

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
        user_id FK
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
        requirement_id FK
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

## Relationship meanings

- **USERS — submits → APPLICATIONS:** A registered user can submit many applications. `applications.user_id` may be empty for public applications.
- **SCHOLARSHIPS — receives → APPLICATIONS:** Each application is for one scholarship; a scholarship can receive many applications.
- **SCHOLARSHIPS — defines → SCHOLARSHIP_REQUIREMENTS:** A scholarship can define multiple requirements.
- **APPLICATIONS — includes → APPLICATION_DOCUMENTS:** An application can include multiple uploaded documents.
- **SCHOLARSHIP_REQUIREMENTS — is referenced by → APPLICATION_DOCUMENTS:** A document may refer to a requirement; `requirement_id` may be empty.

**PK** = Primary Key. **FK** = Foreign Key.

The companion `docs/drawdb_erd.sql` file is for importing the schema into DrawDB. SQL imports need column definitions to construct tables, but the diagram itself can be displayed without data types. This ERD documentation intentionally omits all data types.
