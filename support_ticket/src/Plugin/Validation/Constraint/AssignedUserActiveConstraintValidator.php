<?php

declare(strict_types=1);

namespace Drupal\support_ticket\Plugin\Validation\Constraint;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\user\UserInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates that the assigned user is an active account.
 */
class AssignedUserActiveConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $items, Constraint $constraint): void {
    assert($constraint instanceof AssignedUserActiveConstraint);

    if (!$items instanceof FieldItemListInterface || $items->isEmpty()) {
      return;
    }

    $user = $items->entity;
    if (!$user instanceof UserInterface) {
      return;
    }

    if (!$user->isActive()) {
      $this->context->addViolation($constraint->message);
    }
  }

}
