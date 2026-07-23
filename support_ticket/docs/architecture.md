## Architecture Overview

The Support Ticket module is built using a custom Drupal 11 Content Entity architecture. The module follows Drupal best practices by separating responsibilities into entity management, access control, business logic, validation, and presentation layers.

### High-Level Architecture

```text
                +----------------------+
                |        User          |
                +----------+-----------+
                           |
                           v
                +----------------------+
                |   Entity Forms       |
                | (Create/Edit/Delete) |
                +----------+-----------+
                           |
                           v
                +----------------------+
                |  SupportTicket Entity|
                +----------+-----------+
                           |
        +------------------+------------------+
        |                                     |
        v                                     v
+----------------------+         +----------------------+
| Access Control       |         | Validation           |
| Handler              |         | Constraints          |
+----------------------+         +----------------------+
                           |
                           v
                +----------------------+
                | TicketManager Service|
                | (Business Logic)     |
                +----------+-----------+
                           |
                           v
                +----------------------+
                | Entity Storage       |
                | (Database)           |
                +----------------------+

                           ^
                           |
                +----------------------+
                | SupportTicket        |
                | ListBuilder          |
                +----------------------+
                
                
                
### Component Responsibilities

- **SupportTicket Entity** – Defines the custom content entity and its base fields.
- **Entity Forms** – Handles creating, editing, and deleting support tickets.
- **Access Control Handler** – Controls entity access based on user permissions.
- **Validation Constraints** – Validates business rules before saving entities.
- **TicketManager Service** – Encapsulates ticket assignment and status transition logic.
- **ListBuilder** – Displays support tickets in a tabular listing with entity operations.
- **Entity Storage** – Persists support ticket data in Drupal's database.
