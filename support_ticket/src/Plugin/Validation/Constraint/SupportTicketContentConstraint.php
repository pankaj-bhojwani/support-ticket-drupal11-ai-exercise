<?php

declare(strict_types=1);

namespace Drupal\support_ticket\Plugin\Validation\Constraint;

use Drupal\Core\Entity\Plugin\Validation\Constraint\CompositeConstraintBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Validation\Attribute\Constraint;
use Symfony\Component\Validator\Attribute\HasNamedArguments;

/**
 * Validates required support ticket content business rules.
 */
#[Constraint(
  id: 'SupportTicketContent',
  label: new TranslatableMarkup('Support ticket content', [], ['context' => 'Validation']),
  type: ['entity']
)]
class SupportTicketContentConstraint extends CompositeConstraintBase {

  /**
   * Constructs a SupportTicketContentConstraint object.
   */
  #[HasNamedArguments]
  public function __construct(
    mixed $options = NULL,
    public string $titleMessage = 'The ticket title cannot be empty.',
    public string $descriptionMessage = 'The ticket description cannot be empty.',
    ?array $groups = NULL,
    mixed $payload = NULL,
  ) {
    parent::__construct($options, $groups, $payload);
  }

  /**
   * {@inheritdoc}
   */
  public function coversFields() {
    return ['title', 'description'];
  }

}
