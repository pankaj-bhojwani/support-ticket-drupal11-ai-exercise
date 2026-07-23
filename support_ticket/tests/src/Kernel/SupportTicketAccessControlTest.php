<?php

declare(strict_types=1);

namespace Drupal\Tests\support_ticket\Kernel;

use Drupal\Core\Session\AccountInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\support_ticket\Entity\SupportTicket;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Tests support ticket access control.
 *
 * @group support_ticket
 */
class SupportTicketAccessControlTest extends KernelTestBase {

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
   * The support ticket access control handler.
   *
   * @var \Drupal\Core\Entity\EntityAccessControlHandlerInterface
   */
  protected $accessHandler;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('support_ticket');
    $this->installConfig(['filter']);

    $this->accessHandler = $this->container
      ->get('entity_type.manager')
      ->getAccessControlHandler('support_ticket');
  }

  /**
   * Tests view access for own and any tickets.
   */
  public function testViewAccess(): void {
    $owner = $this->createUser(['view own support tickets']);
    $other = $this->createUser(['view own support tickets']);
    $agent = $this->createUser(['view any support tickets']);

    $ticket = $this->createTicket($owner);

    $this->assertTrue($this->accessHandler->access($ticket, 'view', $owner));
    $this->assertFalse($this->accessHandler->access($ticket, 'view', $other));
    $this->assertTrue($this->accessHandler->access($ticket, 'view', $agent));
  }

  /**
   * Tests update access for own and any tickets.
   */
  public function testUpdateAccess(): void {
    $owner = $this->createUser(['edit own support tickets']);
    $other = $this->createUser(['edit own support tickets']);
    $agent = $this->createUser(['edit any support tickets']);

    $ticket = $this->createTicket($owner);

    $this->assertTrue($this->accessHandler->access($ticket, 'update', $owner));
    $this->assertFalse($this->accessHandler->access($ticket, 'update', $other));
    $this->assertTrue($this->accessHandler->access($ticket, 'update', $agent));
  }

  /**
   * Tests delete access requires the delete-any permission.
   */
  public function testDeleteAccess(): void {
    $owner = $this->createUser([
      'view own support tickets',
      'edit own support tickets',
    ]);
    $admin = $this->createUser(['delete any support tickets']);

    $ticket = $this->createTicket($owner);

    $this->assertFalse($this->accessHandler->access($ticket, 'delete', $owner));
    $this->assertTrue($this->accessHandler->access($ticket, 'delete', $admin));
  }

  /**
   * Tests create access.
   */
  public function testCreateAccess(): void {
    $creator = $this->createUser(['create support tickets']);
    $viewer = $this->createUser(['view own support tickets']);

    $this->assertTrue($this->accessHandler->createAccess(NULL, $creator));
    $this->assertFalse($this->accessHandler->createAccess(NULL, $viewer));
  }

  /**
   * Tests that administer permission grants full access.
   */
  public function testAdministerAccess(): void {
    $owner = $this->createUser();
    $admin = $this->createUser(['administer support tickets']);
    $ticket = $this->createTicket($owner);

    $this->assertTrue($this->accessHandler->access($ticket, 'view', $admin));
    $this->assertTrue($this->accessHandler->access($ticket, 'update', $admin));
    $this->assertTrue($this->accessHandler->access($ticket, 'delete', $admin));
    $this->assertTrue($this->accessHandler->createAccess(NULL, $admin));
  }

  /**
   * Creates a support ticket owned by the given account.
   *
   * @param \Drupal\Core\Session\AccountInterface $owner
   *   The ticket owner.
   *
   * @return \Drupal\support_ticket\Entity\SupportTicket
   *   The saved ticket.
   */
  protected function createTicket(AccountInterface $owner): SupportTicket {
    $ticket = SupportTicket::create([
      'title' => 'Access test ticket',
      'description' => 'Used for access control assertions.',
      'priority' => 'normal',
      'status' => 'open',
      'category' => 'general',
      'uid' => $owner->id(),
    ]);
    $ticket->save();
    // Reset static access cache between assertions.
    $this->accessHandler->resetCache();
    return $ticket;
  }

}
