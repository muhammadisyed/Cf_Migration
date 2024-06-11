<?php

namespace Drupal\cf_migration\Plugin\migrate\source;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * The 'client_company_banner_migration' source plugin.
 *
 * @MigrateSource(
 *   id = "client_company_banner_migration",
 *   source_module = "cf_migration"
 * )
 */
class ClientCompanyBannerMigration extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node', 'n');
    $query->leftJoin('field_data_field_client_banner', 'fbi', 'fbi.entity_id=n.nid');
    $query->leftJoin('file_managed', 'fm', 'fm.fid=fbi.field_client_banner_fid');
    $query->addField('fm', 'fid');
    $query->addField('fm', 'uid');
    $query->addField('fm', 'filename');
    $query->addField('fm', 'uri');
    $query->addField('fm', 'filemime');
    $query->addField('fm', 'filesize');
    $query->addField('fm', 'status');
    $query->addField('fm', 'timestamp');
    $query->condition('n.type', 'clients');
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
    $ids['fid'] = [
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
    $timestamp = (int) $row->get('timestamp');
    $date = DrupalDateTime::createFromTimestamp($timestamp);
    $formatted_date = $date->format('Y-m');

    $row->setSourceProperty('filepath', $formatted_date . '/' . $row->get('filename'));
    $row->setSourceProperty('source_filename', $row->get('filename'));

    return parent::prepareRow($row);
  }

}
