prompt:
The SupportTicket Content Entity and its base fields have been created.

Now generate only the support_ticket.permissions.yml file.

Create permissions for:

- Create support tickets
- View own support tickets
- View any support tickets
- Edit own support tickets
- Edit any support tickets
- Delete any support tickets
- Assign support tickets
- Change support ticket status
- Administer support tickets

Requirements:

- Follow Drupal 11 best practices.
- Add clear titles and descriptions for each permission.
- Explain the purpose of each permission.

Do NOT generate:

- AccessControlHandler
- Forms
- Routing
- Controllers
- Services
- Validation

response:
I'll check the current permissions file and Drupal 11 permission conventions, then write only support_ticket.permissions.yml.

Writing the permissions file to match the entity’s admin_permission and Drupal 11 conventions.

{}
create support tickets:
  title: 'Create support tickets'
  description: 'Submit new support tickets.'
Created web/modules/custom/support_ticket/support_ticket.permissions.yml.

Permission purposes
Permission	Purpose
Create support tickets
Lets authenticated users open new tickets. Typical for end users.
View own support tickets
Lets users see only tickets they submitted. Used for “My tickets” listings and canonical access checks.
View any support tickets
Lets agents/admins see all tickets, not only their own.
Edit own support tickets
Lets users update tickets they created (e.g. clarify description while still open).
Edit any support tickets
Lets agents/admins update any ticket’s fields.
Delete any support tickets
Lets admins remove tickets. No “delete own” permission — deletion is reserved for elevated roles.
Assign support tickets
Controls who can set/clear the assigned_to field. Separate from general edit so assignment can be limited to agents.
Change support ticket status
Controls who can change workflow status (open → in_progress → etc.). Separate so status transitions can be restricted independently of field edits.
Administer support tickets
Module admin permission. Matches the entity’s admin_permission. Marked restrict access: true so Drupal warns before granting it on the permissions page.
Machine names use spaces (Drupal convention), e.g. create support tickets. These strings are what AccessControlHandler and role configuration will check later — that handler was not generated in this step.