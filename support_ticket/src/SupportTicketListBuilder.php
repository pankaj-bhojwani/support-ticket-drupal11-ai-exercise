<?php

declare(strict_types=1);

namespace Drupal\support_ticket;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\support_ticket\Entity\SupportTicket;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a list builder for support ticket entities.
 */
class SupportTicketListBuilder extends EntityListBuilder {

  /**
   * Constructs a new SupportTicketListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $dateFormatter
   *   The date formatter service.
   */
  public function __construct(
    EntityTypeInterface $entity_type,
    EntityStorageInterface $storage,
    protected DateFormatterInterface $dateFormatter,
  ) {
    parent::__construct($entity_type, $storage);
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $container->get('date.formatter'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
    $header['title'] = $this->t('Title');
    $header['status'] = $this->t('Status');
    $header['priority'] = $this->t('Priority');
    $header['category'] = $this->t('Category');
    $header['assigned_to'] = $this->t('Assigned user');
    $header['owner'] = $this->t('Owner');
    $header['created'] = $this->t('Created');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    assert($entity instanceof SupportTicket);

    $row['id'] = $entity->id();
    $row['title']['data'] = [
      '#type' => 'link',
      '#title' => $entity->label(),
      '#url' => $entity->toUrl(),
    ];
    $row['status'] = $this->getListFieldLabel($entity, 'status');
    $row['priority'] = $this->getListFieldLabel($entity, 'priority');
    $row['category'] = $this->getListFieldLabel($entity, 'category');

    $assigned = $entity->get('assigned_to')->entity;
    $row['assigned_to']['data'] = $assigned
      ? [
        '#theme' => 'username',
        '#account' => $assigned,
      ]
      : [
        '#markup' => $this->t('Unassigned'),
      ];

    $row['owner']['data'] = [
      '#theme' => 'username',
      '#account' => $entity->getOwner(),
    ];
    $row['created'] = $this->dateFormatter->format((int) $entity->get('created')->value, 'short');

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntityIds() {
    $query = $this->getStorage()->getQuery()
      ->accessCheck(TRUE)
      ->sort('created', 'DESC');

    // Only add the pager if a limit is specified.
    if ($this->limit) {
      $query->pager($this->limit);
    }

    return $query->execute();
  }

  /**
   * Returns the human-readable label for a list_string field value.
   *
   * @param \Drupal\support_ticket\Entity\SupportTicket $entity
   *   The support ticket entity.
   * @param string $field_name
   *   The field name.
   *
   * @return string|\Drupal\Core\StringTranslation\TranslatableMarkup
   *   The allowed-value label, or the raw value if no label is defined.
   */
  protected function getListFieldLabel(SupportTicket $entity, string $field_name) {
    $item = $entity->get($field_name);
    $value = $item->value;
    $allowed_values = $item->getFieldDefinition()->getSetting('allowed_values') ?? [];
    return $allowed_values[$value] ?? $value;
  }

}
