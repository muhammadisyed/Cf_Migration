<?php

namespace Drupal\cf_migration\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * The 'users_migration' source plugin.
 *
 * @MigrateSource(
 *   id = "users_migration",
 *   source_module = "cf_migration"
 * )
 */
class UsersMigration extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('users', 'u')
      ->fields('u', [
        'uid',
        'name',
        'mail',
        'created',
        'access',
        'login',
        'timezone',
        'status',
      ])
      ->condition('u.uid', 0, '!=');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [];
  }


  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['uid'] = [
      'type' => 'integer',
      'unsigned' => TRUE,
      'size' => 'big',
    ];
    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

    return parent::prepareRow($row);
  }

}
