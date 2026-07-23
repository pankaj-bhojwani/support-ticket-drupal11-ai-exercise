# Support Ticket Module

## Overview

The Support Ticket module is a custom Drupal 11 module that implements a Support Ticket Management System using a custom Content Entity.

It demonstrates Drupal 11 best practices including:

- Custom Content Entity
- Entity Forms
- Entity Access Control
- List Builder
- Custom Permissions
- Dependency Injection
- Validation Constraints
- Custom Services
- Kernel & Functional Tests

---

## Features

- Create Support Tickets
- View Support Tickets
- Edit Support Tickets
- Delete Support Tickets
- Ticket Status Management
- Priority and Category Support
- Ticket Assignment
- Entity Access Control
- Custom Validation
- Custom List Builder

---

## Installation

Enable the module using Drush:

```bash
drush en support_ticket -y
```

Rebuild caches:

```bash
drush cr
```

---

## Module Routes

| Function | Route |
|----------|-------|
| Ticket Listing | `/support/tickets` |
| Add Ticket | `/support/tickets/add` |
| View Ticket | `/support/ticket/{support_ticket}` |
| Edit Ticket | `/support/ticket/{support_ticket}/edit` |
| Delete Ticket | `/support/ticket/{support_ticket}/delete` |

---

## AI-Assisted Development

This module was developed incrementally using AI assistance.

The implementation was broken into 15 focused prompts covering:

1. Planning
2. Module Skeleton
3. Content Entity
4. Base Fields
5. Permissions
6. Access Control
7. Entity Forms
8. Routing
9. List Builder
10. TicketManager Service
11. Validation
12. Navigation
13. Testing
14. Code Review
15. Integration Review

Each AI-generated response was:

- Reviewed
- Validated
- Refined where necessary
- Integrated into the project

The prompt history is available in the `ai-prompts/` directory.

---

## Testing

The module includes:

- Kernel Tests
- Functional Tests

Run tests using PHPUnit:

```bash
vendor/bin/phpunit web/modules/custom/support_ticket/tests
```

---

## Project Structure

```
support_ticket/
├── ai-prompts/
├── src/
├── tests/
├── README.md
├── support_ticket.info.yml
├── support_ticket.permissions.yml
├── support_ticket.routing.yml
├── support_ticket.services.yml
├── support_ticket.links.menu.yml
├── support_ticket.links.task.yml
└── support_ticket.links.action.yml
```

---

## Screenshots

Sample screenshots demonstrating the Support Ticket module workflow are available in the `docs/Screenshots/` directory, including:

- Ticket Listing
- Create Support Ticket
- Edit Support Ticket
- Delete Support Ticket

## Project Information

**Developer:** Pankaj Bhojwani

**Project:** Support Ticket Management System

**Technology:** Drupal 11

**AI Tool Used:** Cursor AI

**Purpose:** AI Capability Exercise demonstrating AI-assisted software development across planning, implementation, testing, documentation, and code review.
