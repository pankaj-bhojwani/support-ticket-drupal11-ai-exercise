prompt:
The SupportTicket Content Entity, AccessControlHandler, Entity Forms, and ListBuilder have been created.

Now generate only the TicketManager service.

Requirements:

- Follow Drupal 11 best practices.
- Use Dependency Injection.
- Create methods for:
  - Assigning a ticket to a user.
  - Updating ticket status.
  - Validating allowed status transitions.
- Keep all business logic inside the service.
- Explain the purpose of each method before generating the code.

Do NOT generate:

- Controllers
- Forms
- Routing
- Event Subscribers
- Queue Workers
- Validation Constraints

response:
I'll review the entity status values and services scaffold, then implement only TicketManager with DI.

Method purposes
Method	Purpose
assignTicket()
Sets or clears assigned_to, rejects blocked users, saves the ticket.
updateStatus()
Changes status only after transition validation, then saves.
isValidStatusTransition()
Returns whether $from → $to is allowed.
getAllowedTransitions()
Returns valid next statuses from a given status (for UI later).
assertValidStatusTransition()
Throws if a transition is invalid (used by updateStatus()).
Business rules stay in this service: allowed workflow map, assignee must be active, unassign via NULL. Callers still enforce assign support tickets / change support ticket status.

<?php
declare(strict_types=1);
services:
  support_ticket.ticket_manager:
    class: Drupal\support_ticket\Service\TicketManager
    arguments:
Replacing bare t() in the service with StringTranslationTrait per Drupal practice.

use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\support_ticket\Entity\SupportTicket;
      '@current' => $assignee ? $assignee->id() : 'unassigned',
      'link' => $ticket->toLink(t('View'))->toString(),
      'link' => $ticket->toLink($this->t('View'))->toString(),
    ]);
      '@to' => $new_status,
      'link' => $ticket->toLink(t('View'))->toString(),
      'link' => $ticket->toLink($this->t('View'))->toString(),
    ]);
Created TicketManager and registered it as support_ticket.ticket_manager.

Files
File	Role
src/Service/TicketManager.php
Assignment + status workflow logic
support_ticket.services.yml
DI registration with @logger.factory
API
$manager = \Drupal::service('support_ticket.ticket_manager');
$manager->assignTicket($ticket, $user);   // or NULL to unassign
$manager->updateStatus($ticket, 'in_progress');
$manager->isValidStatusTransition('open', 'closed');
$manager->getAllowedTransitions('open');
Allowed transitions
open         → in_progress, closed
in_progress  → open, resolved, closed
resolved     → in_progress, closed
closed       → open
Same-status updates are treated as valid (no-op transition). Blocked users cannot be assigned.