<?php

declare(strict_types=1);

namespace Drupal\support_ticket\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for creating and editing support tickets.
 */
class SupportTicketForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\support_ticket\Entity\SupportTicket $ticket */
    $ticket = $this->entity;

    if ($this->operation === 'edit') {
      $form['#title'] = $this->t('Edit support ticket %title', [
        '%title' => $ticket->label(),
      ]);
    }
    else {
      $form['#title'] = $this->t('Create support ticket');
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $ticket = $this->entity;
    $status = parent::save($form, $form_state);

    $t_args = [
      '%title' => $ticket->toLink()->toString(),
    ];

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('Support ticket %title has been created.', $t_args));
        $this->logger('support_ticket')->notice('Created support ticket %title.', [
          '%title' => $ticket->label(),
          'link' => $ticket->toLink($this->t('View'))->toString(),
        ]);
        break;

      default:
        $this->messenger()->addStatus($this->t('Support ticket %title has been updated.', $t_args));
        $this->logger('support_ticket')->notice('Updated support ticket %title.', [
          '%title' => $ticket->label(),
          'link' => $ticket->toLink($this->t('View'))->toString(),
        ]);
        break;
    }

    $form_state->setRedirectUrl($ticket->toUrl('canonical'));

    return $status;
  }

}
