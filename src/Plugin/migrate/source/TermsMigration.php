<?php

namespace Drupal\cf_migration\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * The 'terms_migration' source plugin.
 *
 * @MigrateSource(
 *   id = "terms_migration",
 *   source_module = "cf_migration"
 * )
 */
class TermsMigration extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('taxonomy_term_data', 'ttd')
      ->fields('ttd', ['tid', 'vid', 'name', 'description', 'weight'])
      ->condition('ttd.vid', 4);
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
    $ids['tid'] = [
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
