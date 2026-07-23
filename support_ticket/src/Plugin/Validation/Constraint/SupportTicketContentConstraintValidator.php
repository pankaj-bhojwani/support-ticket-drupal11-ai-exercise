<?php

declare(strict_types=1);

namespace Drupal\support_ticket\Plugin\Validation\Constraint;

use Drupal\support_ticket\Entity\SupportTicket;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates that ticket title and description contain meaningful content.
 */
class SupportTicketContentConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $entity, Constraint $constraint): void {
    assert($constraint instanceof SupportTicketContentConstraint);

    if (!$entity instanceof SupportTicket) {
      return;
    }

    $title = trim((string) $entity->get('title')->value);
    if ($title === '') {
      $this->context->buildViolation($constraint->titleMessage)
        ->atPath('title')
        ->addViolation();
    }

    $description = trim((string) $entity->get('description')->value);
    if ($description === '') {
      $this->context->buildViolation($constraint->descriptionMessage)
        ->atPath('description')
        ->addViolation();
    }
  }

}
