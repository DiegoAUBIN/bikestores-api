# BikeStores API Project Structure

```text
.
|-- SAE401.sql
|-- bootstrap/
|-- config/
|-- docs/
|   `-- project-structure.md
|-- public/
|   `-- docs/
|-- routes/
|-- sql/
|   `-- 01_create_tables.sql
|-- src/
|   |-- Controller/
|   |-- Entity/
|   |-- Exception/
|   |-- Http/
|   |-- Middleware/
|   |-- Model/
|   |-- Repository/
|   |-- Service/
|   `-- View/
|-- tests/
`-- var/
    |-- cache/
    `-- log/
```

## Roles of the Main Directories

- `public/`: web root, front controller, `.htaccess`, Swagger entry point.
- `src/Controller/`: MVC controllers and REST endpoints.
- `src/Entity/`: Doctrine entities mapped to the database tables.
- `src/Repository/`: Doctrine repositories and query methods.
- `src/Service/`: business rules and application services.
- `src/Http/`: request and response helpers, JSON output handling.
- `src/Middleware/`: API key checks, authentication guards, shared HTTP pipeline logic.
- `src/Exception/`: custom exceptions and API error mapping.
- `src/Model/`: DTOs, filters, payload objects, form models.
- `src/View/`: documentation landing page and optional server-rendered templates.
- `config/`: Doctrine configuration, environment settings, service wiring.
- `bootstrap/`: application bootstrap and dependency initialization.
- `routes/`: route declarations or route loader files.
- `sql/`: SQL scripts for schema creation and seed data.
- `var/cache/`: temporary cache files.
- `var/log/`: application logs.
- `tests/`: automated tests for controllers, services, and repositories.