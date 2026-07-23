# Support Ticket Module Requirements

## Project Objective

Develop a custom Drupal 11 module that implements a Support Ticket Management System using Drupal best practices and an incremental AI-assisted development workflow.

---

## Functional Requirements

The module should provide the following functionality:

- Create support tickets
- View support tickets
- Edit support tickets
- Delete support tickets
- Assign tickets to users
- Update ticket status
- Manage ticket priority
- Categorize tickets
- Display support tickets in a tabular listing
- Enforce role-based access control
- Validate business rules before saving tickets

---

## Non-Functional Requirements

The implementation should:

- Be compatible with Drupal 11
- Use a custom Content Entity
- Follow Drupal Coding Standards (PSR-12)
- Use Dependency Injection where applicable
- Implement custom Access Control
- Implement custom Validation Constraints
- Include custom services for business logic
- Include Kernel and Functional tests
- Maintain a modular and reusable architecture

---

## Deliverables

The completed module includes:

- Custom SupportTicket Content Entity
- Entity Forms (Create, Edit, Delete)
- Access Control Handler
- ListBuilder
- TicketManager Service
- Validation Constraints
- Menu Links and Local Actions
- Kernel Tests
- Functional Tests
- Project Documentation
- AI Prompt History

---

## AI Capability Exercise

The module was developed incrementally using AI assistance.

The development process consisted of planning, implementation, validation, testing, code review, and integration review. Each phase was documented in the `ai-prompts/` directory, with AI-generated output reviewed and refined before integration into the project.
