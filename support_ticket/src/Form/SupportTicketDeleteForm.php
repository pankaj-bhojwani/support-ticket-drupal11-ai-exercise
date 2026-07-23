<?php

declare(strict_types=1);

namespace Drupal\support_ticket\Form;

use Drupal\Core\Entity\ContentEntityDeleteForm;

/**
 * Provides a confirmation form for deleting a support ticket.
 */
class SupportTicketDeleteForm extends ContentEntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the support ticket %title?', [
      '%title' => $this->getEntity()->label(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  protected function getDeletionMessage() {
    return $this->t('The support ticket %title has been deleted.', [
      '%title' => $this->getEntity()->label(),
    ]);
  }

}
