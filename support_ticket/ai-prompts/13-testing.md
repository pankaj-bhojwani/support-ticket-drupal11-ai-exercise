prompt:
The SupportTicket module implementation is complete.

Now generate only the test classes for the module.

Generate:

1. Kernel Tests
- Entity creation
- Entity storage
- Validation
- Access control

2. Functional Tests
- Create a support ticket
- View own support ticket
- Edit own support ticket
- Permission checks

Requirements:

- Follow Drupal 11 testing best practices.
- Use Drupal KernelTestBase and BrowserTestBase where appropriate.
- Explain the purpose of each test class before generating the code.

Do NOT generate:

- Production module code
- Controllers
- Forms
- Services
- Routing

response:
I'll review the testing prompt and module state, then generate kernel and functional tests only.

Test class purposes
Class	Purpose
SupportTicketEntityTest
Kernel: create tickets and verify save/load/update storage.
SupportTicketValidationTest
Kernel: status transitions, active assignee, nonempty title/description.
SupportTicketAccessControlTest
Kernel: view/update/delete/create via AccessControlHandler.
SupportTicketFunctionalTest
Browser: create/view/edit own tickets and HTTP permission checks.
<?php
declare(strict_types=1);
<?php
declare(strict_types=1);
<?php
declare(strict_types=1);
<?php
declare(strict_types=1);
use Drupal\Tests\user\Traits\UserCreationTrait;
use Drupal\user\Entity\User;
Created four test classes under tests/src/.

Kernel tests
Class	Covers
SupportTicketEntityTest
Create ticket with required fields; load/update/assign via storage
SupportTicketValidationTest
Invalid/valid status transitions; blocked vs active assignee; whitespace title/description
SupportTicketAccessControlTest
View/update own vs any; delete-any; create; administer bypass
Functional tests
Class	Covers
SupportTicketFunctionalTest
UI create; view own; edit own; 403 for anonymous/other user/no-create
Run them
./vendor/bin/phpunit -c web/core \
  web/modules/custom/support_ticket/tests
Or by group:

./vendor/bin/phpunit -c web/core --group support_ticket