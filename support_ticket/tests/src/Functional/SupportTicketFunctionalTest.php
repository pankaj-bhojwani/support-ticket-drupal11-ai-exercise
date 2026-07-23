<?php

declare(strict_types=1);

namespace Drupal\Tests\support_ticket\Functional;

use Drupal\support_ticket\Entity\SupportTicket;
use Drupal\Tests\BrowserTestBase;

/**
 * Functional tests for creating, viewing, and editing support tickets.
 *
 * @group support_ticket
 */
class SupportTicketFunctionalTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'support_ticket',
    'field',
    'text',
    'filter',
    'user',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * A user who can create and manage their own tickets.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $ticketUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->ticketUser = $this->drupalCreateUser([
      'create support tickets',
      'view own support tickets',
      'edit own support tickets',
    ]);
  }

  /**
   * Tests creating a support ticket through the UI.
   */
  public function testCreateSupportTicket(): void {
    $this->drupalLogin($this->ticketUser);
    $this->drupalGet('/support/tickets/add');
    $this->assertSession()->statusCodeEquals(200);

    $this->submitForm([
      'title[0][value]' => 'Printer not working',
      'description[0][value]' => 'The office printer shows a paper jam error.',
      'priority' => 'high',
      'category' => 'technical',
    ], 'Save');

    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Support ticket Printer not working has been created.');
    $this->assertSession()->pageTextContains('Printer not working');

    $tickets = \Drupal::entityTypeManager()
      ->getStorage('support_ticket')
      ->loadByProperties(['title' => 'Printer not working']);
    $this->assertCount(1, $tickets);
    $ticket = reset($tickets);
    $this->assertEquals($this->ticketUser->id(), $ticket->getOwnerId());
  }

  /**
   * Tests that a user can view their own support ticket.
   */
  public function testViewOwnSupportTicket(): void {
    $ticket = $this->createTicket([
      'title' => 'My visible ticket',
      'uid' => $this->ticketUser->id(),
    ]);

    $this->drupalLogin($this->ticketUser);
    $this->drupalGet($ticket->toUrl());
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('My visible ticket');
  }

  /**
   * Tests that a user can edit their own support ticket.
   */
  public function testEditOwnSupportTicket(): void {
    $ticket = $this->createTicket([
      'title' => 'Ticket to edit',
      'uid' => $this->ticketUser->id(),
    ]);

    $this->drupalLogin($this->ticketUser);
    $this->drupalGet($ticket->toUrl('edit-form'));
    $this->assertSession()->statusCodeEquals(200);

    $this->submitForm([
      'title[0][value]' => 'Ticket edited',
      'description[0][value]' => 'Updated description for the ticket.',
    ], 'Save');

    $this->assertSession()->pageTextContains('Support ticket Ticket edited has been updated.');

    $storage = \Drupal::entityTypeManager()->getStorage('support_ticket');
    $storage->resetCache([$ticket->id()]);
    $reloaded = $storage->load($ticket->id());
    $this->assertEquals('Ticket edited', $reloaded->label());
  }

  /**
   * Tests permission checks for create, view, and edit routes.
   */
  public function testPermissionChecks(): void {
    $owner = $this->ticketUser;
    $ticket = $this->createTicket([
      'title' => 'Owner only ticket',
      'uid' => $owner->id(),
    ]);

    // Anonymous users cannot create or view tickets.
    $this->drupalGet('/support/tickets/add');
    $this->assertSession()->statusCodeEquals(403);
    $this->drupalGet($ticket->toUrl());
    $this->assertSession()->statusCodeEquals(403);

    // A different authenticated user without "any" permissions is denied.
    $other_user = $this->drupalCreateUser([
      'create support tickets',
      'view own support tickets',
      'edit own support tickets',
    ]);
    $this->drupalLogin($other_user);
    $this->drupalGet($ticket->toUrl());
    $this->assertSession()->statusCodeEquals(403);
    $this->drupalGet($ticket->toUrl('edit-form'));
    $this->assertSession()->statusCodeEquals(403);

    // A user without create permission cannot open the add form.
    $viewer = $this->drupalCreateUser([
      'view own support tickets',
    ]);
    $this->drupalLogin($viewer);
    $this->drupalGet('/support/tickets/add');
    $this->assertSession()->statusCodeEquals(403);

    // Owner retains access.
    $this->drupalLogin($owner);
    $this->drupalGet($ticket->toUrl());
    $this->assertSession()->statusCodeEquals(200);
    $this->drupalGet($ticket->toUrl('edit-form'));
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Creates a support ticket for functional tests.
   *
   * @param array $values
   *   Optional field values.
   *
   * @return \Drupal\support_ticket\Entity\SupportTicket
   *   The saved ticket.
   */
  protected function createTicket(array $values = []): SupportTicket {
    $ticket = SupportTicket::create($values + [
      'title' => 'Functional test ticket',
      'description' => 'Created for functional browser tests.',
      'priority' => 'normal',
      'status' => 'open',
      'category' => 'general',
      'uid' => $this->ticketUser->id(),
    ]);
    $ticket->save();
    return $ticket;
  }

}
