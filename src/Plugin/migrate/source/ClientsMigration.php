<?php

namespace Drupal\cf_migration\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * The 'clients_migration' source plugin.
 *
 * @MigrateSource(
 *   id = "clients_migration",
 *   source_module = "cf_migration"
 * )
 */
class ClientsMigration extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node', 'n')
      ->fields('n', [
        'nid',
        'vid',
        'uid',
        'title',
        'created',
        'changed',
        'status',
      ])
      ->condition('n.type', 'clients');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = $this->baseFields();
    $fields['body/format'] = $this->t('Format of body');
    $fields['body/value'] = $this->t('Full text of body');
    $fields['body/summary'] = $this->t('Summary of body');
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['nid'] = [
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
    $nid = $row->getSourceProperty('nid');
    $results = $this->getDatabase()->query('
      SELECT
        fdb.body_value,
        fdb.body_summary,
        fdb.body_format
      FROM
        {field_data_body} fdb
      WHERE
        fdb.entity_id = :nid
    ', array(':nid' => $nid));
    foreach($results as $result) {
      $row->setSourceProperty('body_value', $result->body_value);
      $row->setSourceProperty('body_summary', $result->body_summary);
      $row->setSourceProperty('body_format', $result->body_format);
    }

    $result = $this->getDatabase()->query('
      SELECT
        fbi.field_client_company_logo_fid,
        fbi.field_client_company_logo_alt,
        fbi.field_client_company_logo_title,
        fbi.field_client_company_logo_width,
        fbi.field_client_company_logo_height
      FROM
        {field_data_field_client_company_logo} fbi
      WHERE
        fbi.entity_id = :nid
    ', array(':nid' => $nid));

    $images = [];
    foreach ($result as $record) {
      $images[] = [
        'target_id' => $record->field_client_company_logo_fid,
        'alt' => $record->field_client_company_logo_alt,
        'title' => $record->field_client_company_logo_title,
        'width' => $record->field_client_company_logo_width,
        'height' => $record->field_client_company_logo_height,
      ];
    }

    $row->setSourceProperty('logo_images', $images);

    return parent::prepareRow($row);
  }

}
