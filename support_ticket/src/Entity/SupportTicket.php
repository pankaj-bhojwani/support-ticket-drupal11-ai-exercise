<?php

declare(strict_types=1);

namespace Drupal\support_ticket\Entity;

use Drupal\Core\Entity\Attribute\ContentEntityType;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\support_ticket\Form\SupportTicketDeleteForm;
use Drupal\support_ticket\Form\SupportTicketForm;
use Drupal\support_ticket\SupportTicketAccessControlHandler;
use Drupal\support_ticket\SupportTicketListBuilder;
use Drupal\user\EntityOwnerInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the Support Ticket entity class.
 */
#[ContentEntityType(
  id: 'support_ticket',
  label: new TranslatableMarkup('Support ticket'),
  label_collection: new TranslatableMarkup('Support tickets'),
  label_singular: new TranslatableMarkup('support ticket'),
  label_plural: new TranslatableMarkup('support tickets'),
  entity_keys: [
    'id' => 'id',
    'uuid' => 'uuid',
    'label' => 'title',
    'owner' => 'uid',
    'created' => 'created',
    'changed' => 'changed',
  ],
  handlers: [
    'storage' => SqlContentEntityStorage::class,
    'access' => SupportTicketAccessControlHandler::class,
    'view_builder' => EntityViewBuilder::class,
    'list_builder' => SupportTicketListBuilder::class,
    'form' => [
      'default' => SupportTicketForm::class,
      'add' => SupportTicketForm::class,
      'edit' => SupportTicketForm::class,
      'delete' => SupportTicketDeleteForm::class,
    ],
  ],
  links: [
    'canonical' => '/support/tickets/{support_ticket}',
    'add-form' => '/support/tickets/add',
    'edit-form' => '/support/tickets/{support_ticket}/edit',
    'delete-form' => '/support/tickets/{support_ticket}/delete',
    'collection' => '/support/tickets',
  ],
  admin_permission: 'administer support tickets',
  base_table: 'support_ticket',
  label_count: [
    'singular' => '@count support ticket',
    'plural' => '@count support tickets',
  ],
  constraints: [
    'SupportTicketStatusTransition' => [],
    'SupportTicketContent' => [],
  ],
)]
class SupportTicket extends ContentEntityBase implements EntityOwnerInterface, EntityChangedInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields += static::ownerBaseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('A short summary of the support issue.'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -10,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setDescription(t('Detailed information about the problem or request.'))
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'text_default',
        'weight' => -5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => -5,
        'settings' => [
          'rows' => 6,
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['priority'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Priority'))
      ->setDescription(t('How urgently this ticket needs attention.'))
      ->setRequired(TRUE)
      ->setSetting('allowed_values', [
        'low' => t('Low'),
        'normal' => t('Normal'),
        'high' => t('High'),
        'critical' => t('Critical'),
      ])
      ->setDefaultValue('normal')
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'list_default',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Status'))
      ->setDescription(t('The current stage of the ticket in the support workflow.'))
      ->setRequired(TRUE)
      ->setSetting('allowed_values', [
        'open' => t('Open'),
        'in_progress' => t('In progress'),
        'resolved' => t('Resolved'),
        'closed' => t('Closed'),
      ])
      ->setDefaultValue('open')
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'list_default',
        'weight' => 5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['category'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Category'))
      ->setDescription(t('The type of issue for routing and reporting.'))
      ->setRequired(TRUE)
      ->setSetting('allowed_values', [
        'general' => t('General'),
        'technical' => t('Technical'),
        'billing' => t('Billing'),
        'other' => t('Other'),
      ])
      ->setDefaultValue('general')
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'list_default',
        'weight' => 10,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['assigned_to'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Assigned user'))
      ->setDescription(t('The support agent responsible for this ticket.'))
      ->setSetting('target_type', 'user')
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'entity_reference_label',
        'weight' => 15,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 15,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->addConstraint('AssignedUserActive');

    $fields['uid']
      ->setLabel(t('Owner'))
      ->setDescription(t('The user who submitted the ticket.'))
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'author',
        'weight' => 20,
      ])
      ->setDisplayOptions('form', [
        'region' => 'hidden',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The date and time the ticket was submitted.'))
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'timestamp',
        'weight' => 25,
      ])
      ->setDisplayOptions('form', [
        'region' => 'hidden',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Updated'))
      ->setDescription(t('The date and time the ticket was last updated.'))
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'timestamp',
        'weight' => 30,
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
