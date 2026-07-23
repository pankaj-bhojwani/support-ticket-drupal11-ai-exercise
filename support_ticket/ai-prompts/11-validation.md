prompt:
The SupportTicket Content Entity, AccessControlHandler, Entity Forms, ListBuilder, and TicketManager service have been created.

Now generate only the validation components for the Support Ticket module.

Requirements:

- Follow Drupal 11 best practices.
- Generate custom validation constraints where appropriate.
- Validate:
  - Status transitions
  - Assigned user must be active
  - Required business rules
- Keep validation separate from business logic.
- Explain the purpose of each validation component before generating the code.

Do NOT generate:

- Controllers
- Forms
- Routing
- Services
- Event Subscribers
- Queue Workers

response:
Validation components
Component	Purpose
SupportTicketStatusTransitionConstraint (+ Validator)
Entity-level: blocks illegal status changes by comparing the original status to the new one via TicketManager::isValidStatusTransition() (checks only; does not save).
AssignedUserActiveConstraint (+ Validator)
Field-level on assigned_to: allows empty (unassigned); rejects blocked/inactive users.
SupportTicketContentConstraint (+ Validator)
Entity-level business rule: title and description must be non-empty after trimming whitespace.
Wiring these onto the entity/fields next—no services, forms, or events.

<?php
declare(strict_types=1);
<?php
declare(strict_types=1);
<?php
declare(strict_types=1);
<?php
declare(strict_types=1);
<?php
declare(strict_types=1);
<?php
declare(strict_types=1);
  ],
  constraints: [
    'SupportTicketStatusTransition' => [],
    'SupportTicketContent' => [],
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
      ->setDisplayConfigurable('view', TRUE)
      ->addConstraint('AssignedUserActive');
Created six validation plugins under src/Plugin/Validation/Constraint/ and wired them onto the entity.

Components
Constraint	Level	What it enforces
SupportTicketStatusTransition
Entity
Illegal status changes (uses TicketManager::isValidStatusTransition() for the rule map only)
AssignedUserActive
Field (assigned_to)
Assignee must be an active user; empty is allowed
SupportTicketContent
Entity
Title and description must not be blank/whitespace-only
Separation from business logic
Validation reports violations on $entity->validate() / form save — it does not assign or change status.
TicketManager still performs assignment/status updates and can throw for direct service calls.
Transition rules stay defined once in TicketManager; the status validator only asks whether a transition is allowed.
Wiring
constraints: [
  'SupportTicketStatusTransition' => [],
  'SupportTicketContent' => [],
],
assigned_to field: ->addConstraint('AssignedUserActive').