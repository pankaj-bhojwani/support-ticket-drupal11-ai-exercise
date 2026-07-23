<?php

declare(strict_types=1);

namespace Drupal\Tests\support_ticket\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\support_ticket\Entity\SupportTicket;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Tests support ticket validation constraints.
 *
 * @group support_ticket
 */
class SupportTicketValidationTest extends KernelTestBase {

  use UserCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'text',
    'filter',
    'support_ticket',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('support_ticket');
    $this->installConfig(['filter']);
  }

  /**
   * Tests that invalid status transitions are rejected.
   */
  public function testInvalidStatusTransition(): void {
    $ticket = $this->createTicket([
      'status' => 'open',
    ]);

    // open -> resolved is not an allowed transition.
    $ticket->set('status', 'resolved');
    $violations = $ticket->validate();

    $this->assertGreaterThan(0, $violations->count());
    $this->assertTrue($this->hasViolationForPath($violations, 'status'));
  }

  /**
   * Tests that a valid status transition passes validation.
   */
  public function testValidStatusTransition(): void {
    $ticket = $this->createTicket([
      'status' => 'open',
    ]);

    $ticket->set('status', 'in_progress');
    $violations = $ticket->validate();

    $this->assertCount(0, $violations);
  }

  /**
   * Tests that blocked users cannot be assigned.
   */
  public function testAssignedUserMustBeActive(): void {
    $blocked = $this->createUser();
    $blocked->block();
    $blocked->save();

    $ticket = $this->createTicket();
    $ticket->set('assigned_to', $blocked->id());
    $violations = $ticket->validate();

    $this->assertGreaterThan(0, $violations->count());
    $this->assertTrue($this->hasViolationForPath($violations, 'assigned_to'));
  }

  /**
   * Tests that an active assignee passes validation.
   */
  public function testAssignedUserActivePasses(): void {
    $assignee = $this->createUser();
    $ticket = $this->createTicket();
    $ticket->set('assigned_to', $assignee->id());
    $violations = $ticket->validate();

    $this->assertCount(0, $violations);
  }

  /**
   * Tests that whitespace-only title and description fail validation.
   */
  public function testRequiredContentBusinessRules(): void {
    $ticket = $this->createTicket();
    $ticket->set('title', '   ');
    $ticket->set('description', "\n\t");
    $violations = $ticket->validate();

    $this->assertGreaterThan(0, $violations->count());
    $this->assertTrue($this->hasViolationForPath($violations, 'title'));
    $this->assertTrue($this->hasViolationForPath($violations, 'description'));
  }

  /**
   * Creates and saves a support ticket for validation tests.
   *
   * @param array $values
   *   Optional field values.
   *
   * @return \Drupal\support_ticket\Entity\SupportTicket
   *   The saved ticket.
   */
  protected function createTicket(array $values = []): SupportTicket {
    $owner = $values['uid'] ?? $this->createUser()->id();
    unset($values['uid']);

    $ticket = SupportTicket::create($values + [
      'title' => 'Validation ticket',
      'description' => 'A ticket used for validation testing.',
      'priority' => 'normal',
      'status' => 'open',
      'category' => 'general',
      'uid' => $owner,
    ]);
    $ticket->save();
    return $ticket;
  }

  /**
   * Checks whether any violation targets the given property path.
   *
   * @param \Symfony\Component\Validator\ConstraintViolationListInterface $violations
   *   The violation list.
   * @param string $path
   *   The property path.
   *
   * @return bool
   *   TRUE if a matching violation exists.
   */
  protected function hasViolationForPath($violations, string $path): bool {
    foreach ($violations as $violation) {
      if ($violation->getPropertyPath() === $path || str_starts_with((string) $violation->getPropertyPath(), $path . '.')) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
