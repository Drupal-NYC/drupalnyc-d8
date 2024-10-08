<?php

/**
 * @file
 * Data adjustments for extra fields.
 */

use Drush\Drush;

/**
 * Migrate from core date field to smart date field.
 */
function dnyc_extra_field_post_update_smart_date_migrate(&$sandbox) {
  return Drush::processManager()->drush(
    Drush::service('site.alias.manager')->getSelf(),
    'smart_date:migrate',
    ['event', 'schema_event_schedule', 'schema_start_date', 'schema_end_date'],)
    ->run();
}
