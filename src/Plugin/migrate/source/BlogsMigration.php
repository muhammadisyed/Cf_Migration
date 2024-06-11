<?php

namespace Drupal\cf_migration\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * The 'blogs_migration' source plugin.
 *
 * @MigrateSource(
 *   id = "blogs_migration",
 *   source_module = "cf_migration"
 * )
 */
class BlogsMigration extends SqlBase {

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
      ->condition('n.type', 'blogs');
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
        fbi.field_blog_image_fid,
        fbi.field_blog_image_alt,
        fbi.field_blog_image_title,
        fbi.field_blog_image_width,
        fbi.field_blog_image_height
      FROM
        {field_data_field_blog_image} fbi
      WHERE
        fbi.entity_id = :nid
    ', array(':nid' => $nid));

    $images = [];
    foreach ($result as $record) {
      $images[] = [
        'target_id' => $record->field_blog_image_fid,
        'alt' => $record->field_blog_image_alt,
        'title' => $record->field_blog_image_title,
        'width' => $record->field_blog_image_width,
        'height' => $record->field_blog_image_height,
      ];
    }

    $row->setSourceProperty('images', $images);

    $result = $this->getDatabase()->query('
      SELECT
        GROUP_CONCAT(fld.field_blog_tags_tid) as tids
      FROM
        {field_data_field_blog_tags} fld
      WHERE
        fld.entity_id = :nid
    ', array(':nid' => $nid));

    foreach ($result as $record) {
      if (!is_null($record->tids)) {
        $row->setSourceProperty('tags', explode(',', $record->tids));
      }
    }

    return parent::prepareRow($row);
  }

}
