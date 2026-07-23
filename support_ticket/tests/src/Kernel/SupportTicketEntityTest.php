<?php

declare(strict_types=1);

namespace Drupal\Tests\support_ticket\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\support_ticket\Entity\SupportTicket;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Tests support ticket entity creation and storage.
 *
 * @group support_ticket
 */
class SupportTicketEntityTest extends KernelTestBase {

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
   * Tests creating a support ticket with required fields.
   */
  public function testEntityCreation(): void {
    $owner = $this->createUser();

    $ticket = SupportTicket::create([
      'title' => 'Cannot log in',
      'description' => 'Password reset emails are not arriving.',
      'priority' => 'high',
      'status' => 'open',
      'category' => 'technical',
      'uid' => $owner->id(),
    ]);
    $ticket->save();

    $this->assertFalse($ticket->isNew());
    $this->assertNotEmpty($ticket->id());
    $this->assertEquals('Cannot log in', $ticket->label());
    $this->assertEquals('high', $ticket->get('priority')->value);
    $this->assertEquals('open', $ticket->get('status')->value);
    $this->assertEquals('technical', $ticket->get('category')->value);
    $this->assertEquals($owner->id(), $ticket->getOwnerId());
    $this->assertNotEmpty($ticket->get('created')->value);
    $this->assertNotEmpty($ticket->get('changed')->value);
  }

  /**
   * Tests loading and updating a stored support ticket.
   */
  public function testEntityStorage(): void {
    $owner = $this->createUser();
    $assignee = $this->createUser();

    $ticket = SupportTicket::create([
      'title' => 'Original title',
      'description' => 'Original description',
      'priority' => 'normal',
      'status' => 'open',
      'category' => 'general',
      'uid' => $owner->id(),
    ]);
    $ticket->save();
    $ticket_id = $ticket->id();

    $storage = $this->container->get('entity_type.manager')->getStorage('support_ticket');
    $storage->resetCache([$ticket_id]);

    /** @var \Drupal\support_ticket\Entity\SupportTicket $loaded */
    $loaded = $storage->load($ticket_id);
    $this->assertInstanceOf(SupportTicket::class, $loaded);
    $this->assertEquals('Original title', $loaded->label());

    $loaded->set('title', 'Updated title');
    $loaded->set('status', 'in_progress');
    $loaded->set('assigned_to', $assignee->id());
    $loaded->save();

    $storage->resetCache([$ticket_id]);
    /** @var \Drupal\support_ticket\Entity\SupportTicket $reloaded */
    $reloaded = $storage->load($ticket_id);

    $this->assertEquals('Updated title', $reloaded->label());
    $this->assertEquals('in_progress', $reloaded->get('status')->value);
    $this->assertEquals($assignee->id(), $reloaded->get('assigned_to')->target_id);
  }

}
