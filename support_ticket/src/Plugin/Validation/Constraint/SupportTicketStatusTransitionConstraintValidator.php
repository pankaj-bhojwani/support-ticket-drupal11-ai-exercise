<?php

declare(strict_types=1);

namespace Drupal\support_ticket\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\support_ticket\Entity\SupportTicket;
use Drupal\support_ticket\Service\TicketManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates support ticket status transitions.
 */
class SupportTicketStatusTransitionConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * Constructs a SupportTicketStatusTransitionConstraintValidator object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\support_ticket\Service\TicketManager $ticketManager
   *   The ticket manager service.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected TicketManager $ticketManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('support_ticket.ticket_manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $entity, Constraint $constraint): void {
    assert($constraint instanceof SupportTicketStatusTransitionConstraint);

    if (!$entity instanceof SupportTicket || $entity->isNew()) {
      return;
    }

    $new_status = (string) $entity->get('status')->value;
    /** @var \Drupal\support_ticket\Entity\SupportTicket|null $original */
    $original = $this->entityTypeManager
      ->getStorage('support_ticket')
      ->loadUnchanged($entity->id());

    if (!$original instanceof SupportTicket) {
      return;
    }

    $original_status = (string) $original->get('status')->value;
    if ($this->ticketManager->isValidStatusTransition($original_status, $new_status)) {
      return;
    }

    $this->context->buildViolation($constraint->message, [
      '%from' => $original_status,
      '%to' => $new_status,
    ])
      ->atPath('status')
      ->addViolation();
  }

}
