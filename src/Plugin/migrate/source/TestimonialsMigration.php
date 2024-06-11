<?php

namespace Drupal\cf_migration\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * The 'testimonials_migration' source plugin.
 *
 * @MigrateSource(
 *   id = "testimonials_migration",
 *   source_module = "cf_migration"
 * )
 */
class TestimonialsMigration extends SqlBase {

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
      ->condition('n.type', 'testimonials');
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

    $results = $this->getDatabase()->query('
      SELECT
        ftc.field_testimonial_company_value
      FROM
        {field_data_field_testimonial_company} ftc
      WHERE
        ftc.entity_id = :nid
    ', array(':nid' => $nid));
    foreach($results as $result) {
      $row->setSourceProperty('company', $result->field_testimonial_company_value);
    }

    $results = $this->getDatabase()->query('
      SELECT
        ftc.field_testimonial_name_value
      FROM
        {field_data_field_testimonial_name} ftc
      WHERE
        ftc.entity_id = :nid
    ', array(':nid' => $nid));
    foreach($results as $result) {
      $row->setSourceProperty('designation', $result->field_testimonial_name_value);
    }

    $results = $this->getDatabase()->query('
      SELECT
        ftv.field_testimonial_video_video_url
      FROM
        {field_data_field_testimonial_video} ftv
      WHERE
        ftv.entity_id = :nid
    ', array(':nid' => $nid));
    foreach($results as $result) {
      $row->setSourceProperty('video', $result->field_testimonial_video_video_url);
    }

    return parent::prepareRow($row);
  }

}
