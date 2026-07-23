<?php

declare(strict_types=1);

namespace Drupal\support_ticket\Plugin\Validation\Constraint;

use Drupal\Core\Entity\Plugin\Validation\Constraint\CompositeConstraintBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Validation\Attribute\Constraint;
use Symfony\Component\Validator\Attribute\HasNamedArguments;

/**
 * Validates that support ticket status transitions are allowed.
 */
#[Constraint(
  id: 'SupportTicketStatusTransition',
  label: new TranslatableMarkup('Support ticket status transition', [], ['context' => 'Validation']),
  type: ['entity']
)]
class SupportTicketStatusTransitionConstraint extends CompositeConstraintBase {

  /**
   * Constructs a SupportTicketStatusTransitionConstraint object.
   */
  #[HasNamedArguments]
  public function __construct(
    mixed $options = NULL,
    public string $message = 'The status transition from %from to %to is not allowed.',
    ?array $groups = NULL,
    mixed $payload = NULL,
  ) {
    parent::__construct($options, $groups, $payload);
  }

  /**
   * {@inheritdoc}
   */
  public function coversFields() {
    return ['status'];
  }

}
