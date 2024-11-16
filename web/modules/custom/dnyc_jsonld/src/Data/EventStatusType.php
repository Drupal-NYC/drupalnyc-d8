<?php

namespace Drupal\dnyc_jsonld\Data;

/**
 * Correlates field values with Google required values.
 */
enum EventStatusType: string {

  case EventScheduled = 'event_scheduled';
  case EventCancelled = 'event_cancelled';
  case EventMovedOnline = 'event_moved_online';
  case EventPostponed = 'event_postponed';
  case EventRescheduled = 'event_rescheduled';

  /**
   * Gets the schema.org url for the property in JSON-LD.
   *
   * @return string
   *   The url.
   */
  public function url(): string {
    return match ($this) {
      self::EventScheduled => 'https://schema.org/EventScheduled',
      self::EventCancelled => 'https://schema.org/EventCancelled',
      self::EventMovedOnline => 'https://schema.org/EventMovedOnline',
      self::EventPostponed => 'https://schema.org/EventPostponed',
      self::EventRescheduled => 'https://schema.org/EventRescheduled',
    };
  }

}
