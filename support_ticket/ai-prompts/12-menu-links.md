prompt:
The SupportTicket module backend implementation is complete.

Now generate only the navigation YAML files for the module.

Generate:

- support_ticket.links.menu.yml
- support_ticket.links.task.yml
- support_ticket.links.action.yml

Requirements:

- Follow Drupal 11 best practices.
- Create menu links for:
  - Support Tickets
  - Add Ticket
  - Admin Ticket Listing
- Create local tasks for:
  - View
  - Edit
- Create a local action for:
  - Add Ticket
- Explain the purpose of each YAML file before generating it.

Do NOT generate:

- Controllers
- Forms
- Routing
- Services
- Validation
- Event Subscribers
- Queue Workers

response:
I'll check the existing routes and link YAML scaffolds, then generate the three navigation files.

File purposes
File	Purpose
links.menu.yml
Sidebar/toolbar menu items (Support Tickets, Add Ticket, admin listing under Content).
links.task.yml
Tabs on a ticket page (View / Edit).
links.action.yml
“Add ticket” button on the collection page.
{}
# User-facing navigation.
support_ticket.collection:
  title: 'Support tickets'
{}
entity.support_ticket.canonical:
  title: 'View'
  route_name: entity.support_ticket.canonical
{}
support_ticket.add_action:
  route_name: entity.support_ticket.add_form
  title: 'Add ticket'
Generated the three navigation YAML files.

Menu links (links.menu.yml)
Link	Where	Route
Support tickets
Tools menu
entity.support_ticket.collection
Add ticket
Child of Support tickets
entity.support_ticket.add_form
Support tickets (admin)
Content (system.admin_content)
Same collection route
There is no separate /admin/support/tickets route yet, so the admin item uses the existing collection.

Local tasks (links.task.yml)
Tab	Route	Base
View
entity.support_ticket.canonical
canonical
Edit
entity.support_ticket.edit_form
canonical
Local action (links.action.yml)
Add ticket appears on the collection page (entity.support_ticket.collection).