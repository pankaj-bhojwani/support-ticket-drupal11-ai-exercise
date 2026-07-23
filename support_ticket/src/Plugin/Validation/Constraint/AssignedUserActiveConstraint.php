<?php

declare(strict_types=1);

namespace Drupal\support_ticket\Plugin\Validation\Constraint;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Validation\Attribute\Constraint;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint as SymfonyConstraint;

/**
 * Validates that an assigned user account is active.
 */
#[Constraint(
  id: 'AssignedUserActive',
  label: new TranslatableMarkup('Assigned user is active', [], ['context' => 'Validation']),
  type: ['entity_reference']
)]
class AssignedUserActiveConstraint extends SymfonyConstraint {

  /**
   * Constructs an AssignedUserActiveConstraint object.
   */
  #[HasNamedArguments]
  public function __construct(
    mixed $options = NULL,
    public string $message = 'Support tickets can only be assigned to active users.',
    ?array $groups = NULL,
    mixed $payload = NULL,
  ) {
    parent::__construct($options, $groups, $payload);
  }

}
