<?php

declare(strict_types=1);

namespace Drupal\support_ticket\Service;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\support_ticket\Entity\SupportTicket;
use Drupal\user\UserInterface;

/**
 * Provides business logic for support ticket assignment and status changes.
 */
class TicketManager {

  use StringTranslationTrait;

  /**
   * Ticket status: newly submitted.
   */
  public const STATUS_OPEN = 'open';

  /**
   * Ticket status: actively being worked on.
   */
  public const STATUS_IN_PROGRESS = 'in_progress';

  /**
   * Ticket status: work completed, awaiting closure.
   */
  public const STATUS_RESOLVED = 'resolved';

  /**
   * Ticket status: fully closed.
   */
  public const STATUS_CLOSED = 'closed';

  /**
   * Allowed status transitions keyed by current status.
   *
   * @var array<string, list<string>>
   */
  protected const ALLOWED_TRANSITIONS = [
    self::STATUS_OPEN => [
      self::STATUS_IN_PROGRESS,
      self::STATUS_CLOSED,
    ],
    self::STATUS_IN_PROGRESS => [
      self::STATUS_OPEN,
      self::STATUS_RESOLVED,
      self::STATUS_CLOSED,
    ],
    self::STATUS_RESOLVED => [
      self::STATUS_IN_PROGRESS,
      self::STATUS_CLOSED,
    ],
    self::STATUS_CLOSED => [
      self::STATUS_OPEN,
    ],
  ];

  /**
   * The support_ticket logger channel.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected LoggerChannelInterface $logger;

  /**
   * Constructs a TicketManager object.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger channel factory.
   */
  public function __construct(LoggerChannelFactoryInterface $logger_factory) {
    $this->logger = $logger_factory->get('support_ticket');
  }

  /**
   * Assigns a support ticket to a user, or clears the assignment.
   *
   * @param \Drupal\support_ticket\Entity\SupportTicket $ticket
   *   The ticket to update.
   * @param \Drupal\user\UserInterface|null $assignee
   *   The user to assign, or NULL to unassign.
   *
   * @return \Drupal\support_ticket\Entity\SupportTicket
   *   The updated ticket.
   *
   * @throws \InvalidArgumentException
   *   Thrown when the assignee account is blocked.
   */
  public function assignTicket(SupportTicket $ticket, ?UserInterface $assignee): SupportTicket {
    if ($assignee instanceof UserInterface && !$assignee->isActive()) {
      throw new \InvalidArgumentException('Support tickets can only be assigned to active users.');
    }

    $previous_assignee_id = $ticket->get('assigned_to')->target_id;
    $ticket->set('assigned_to', $assignee?->id());
    $ticket->save();

    $this->logger->notice('Support ticket %title assigned from @previous to @current.', [
      '%title' => $ticket->label(),
      '@previous' => $previous_assignee_id ?: 'unassigned',
      '@current' => $assignee ? $assignee->id() : 'unassigned',
      'link' => $ticket->toLink($this->t('View'))->toString(),
    ]);

    return $ticket;
  }

  /**
   * Updates the status of a support ticket.
   *
   * @param \Drupal\support_ticket\Entity\SupportTicket $ticket
   *   The ticket to update.
   * @param string $new_status
   *   The target status machine name.
   *
   * @return \Drupal\support_ticket\Entity\SupportTicket
   *   The updated ticket.
   *
   * @throws \InvalidArgumentException
   *   Thrown when the status transition is not allowed.
   */
  public function updateStatus(SupportTicket $ticket, string $new_status): SupportTicket {
    $current_status = (string) $ticket->get('status')->value;
    $this->assertValidStatusTransition($current_status, $new_status);

    $ticket->set('status', $new_status);
    $ticket->save();

    $this->logger->notice('Support ticket %title status changed from @from to @to.', [
      '%title' => $ticket->label(),
      '@from' => $current_status,
      '@to' => $new_status,
      'link' => $ticket->toLink($this->t('View'))->toString(),
    ]);

    return $ticket;
  }

  /**
   * Checks whether a status transition is allowed.
   *
   * @param string $from
   *   The current status machine name.
   * @param string $to
   *   The target status machine name.
   *
   * @return bool
   *   TRUE if the transition is allowed, FALSE otherwise.
   */
  public function isValidStatusTransition(string $from, string $to): bool {
    if ($from === $to) {
      return TRUE;
    }

    $allowed = self::ALLOWED_TRANSITIONS[$from] ?? [];
    return in_array($to, $allowed, TRUE);
  }

  /**
   * Returns the statuses that may follow a given status.
   *
   * @param string $from
   *   The current status machine name.
   *
   * @return list<string>
   *   Allowed next status machine names.
   */
  public function getAllowedTransitions(string $from): array {
    return self::ALLOWED_TRANSITIONS[$from] ?? [];
  }

  /**
   * Asserts that a status transition is allowed.
   *
   * @param string $from
   *   The current status machine name.
   * @param string $to
   *   The target status machine name.
   *
   * @throws \InvalidArgumentException
   *   Thrown when the transition is not allowed.
   */
  public function assertValidStatusTransition(string $from, string $to): void {
    if (!$this->isValidStatusTransition($from, $to)) {
      throw new \InvalidArgumentException(sprintf(
        'Status transition from "%s" to "%s" is not allowed.',
        $from,
        $to,
      ));
    }
  }

}
