<?php

namespace Drupal\dnyc_jsonld\Data;

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
      self::EventScheduled => 'http://schema.org/EventScheduled',
      self::EventCancelled => 'http://schema.org/EventCancelled',
      self::EventMovedOnline => 'http://schema.org/EventMovedOnline',
      self::EventPostponed => 'http://schema.org/EventPostponed',
      self::EventRescheduled => 'http://schema.org/EventRescheduled',
    };
  }

}
