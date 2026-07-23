<?php

declare(strict_types=1);

namespace Drupal\support_ticket;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\support_ticket\Entity\SupportTicket;

/**
 * Defines the access control handler for the support ticket entity type.
 */
class SupportTicketAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    assert($entity instanceof SupportTicket);

    // Administrative permission grants access to every operation.
    if ($account->hasPermission('administer support tickets')) {
      return AccessResult::allowed()->cachePerPermissions();
    }

    $is_owner = (bool) $account->id() && (int) $account->id() === (int) $entity->getOwnerId();

    switch ($operation) {
      case 'view':
        if ($account->hasPermission('view any support tickets')) {
          return AccessResult::allowed()
            ->cachePerPermissions()
            ->addCacheableDependency($entity);
        }
        if ($account->hasPermission('view own support tickets') && $is_owner) {
          return AccessResult::allowed()
            ->cachePerPermissions()
            ->cachePerUser()
            ->addCacheableDependency($entity);
        }
        return AccessResult::neutral()
          ->cachePerPermissions()
          ->cachePerUser()
          ->addCacheableDependency($entity)
          ->setReason("The 'view any support tickets' or 'view own support tickets' permission is required.");

      case 'update':
        if ($account->hasPermission('edit any support tickets')) {
          return AccessResult::allowed()
            ->cachePerPermissions()
            ->addCacheableDependency($entity);
        }
        if ($account->hasPermission('edit own support tickets') && $is_owner) {
          return AccessResult::allowed()
            ->cachePerPermissions()
            ->cachePerUser()
            ->addCacheableDependency($entity);
        }
        return AccessResult::neutral()
          ->cachePerPermissions()
          ->cachePerUser()
          ->addCacheableDependency($entity)
          ->setReason("The 'edit any support tickets' or 'edit own support tickets' permission is required.");

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete any support tickets')
          ->cachePerPermissions()
          ->addCacheableDependency($entity);

      default:
        // No opinion on custom operations such as assign or status change.
        return AccessResult::neutral()->cachePerPermissions();
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermissions($account, [
      'administer support tickets',
      'create support tickets',
    ], 'OR');
  }

}
