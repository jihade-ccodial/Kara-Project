<?php

namespace App\Imports;

use App\Services\Google;
use Auth;
use Carbon\Carbon;
//use Google\Service\Calendar\EventCreator;
use Google_Service_Calendar_ConferenceData;
use Google_Service_Calendar_ConferenceSolutionKey;
use Google_Service_Calendar_CreateConferenceRequest;
use Illuminate\Support\Str;

class GoogleCalendars
{
    public static function get_calendars(): array
    {
        $service = app(Google::class)->connectUser(Auth::user())->service('Calendar');

        $calendars = [];
        if (! $service->getClient()->isAccessTokenExpired()) {
            $pageToken = null;

            do {
                // Ask the sub class to perform an API call with this pageToken (initially null).
                $list = $service->calendarList->listCalendarList(compact('pageToken'));

                foreach ($list->getItems() as $item) {
                    // The sub class is responsible for mapping the data into our database.
                    $calendars[$item->id] = $item->summary;
                }

                // Get the new page token from the response.
                $pageToken = $list->getNextPageToken();

                // Continue until the new page token is null.
            } while ($pageToken);
        }

        return $calendars;
    }

    public static function add_event($calendar_id, $event)
    {
        $service = app(Google::class)->connectUser(Auth::user())->service('Calendar');
        $optionalParameters = ['sendUpdates' => 'all', 'conferenceDataVersion' => 1, 'sendNotifications' => true];
        $event = $service->events->insert($calendar_id, $event, $optionalParameters);

        return $event;
    }

    public static function create_event($calendar_id, $summary, $start, $description, $member)
    {
        // $calendar = self::get_calendar($calendar_id);
        $start = Carbon::parse($start, Auth::user()->timezone)->tz('UTC');
        $event = new \Google_Service_Calendar_Event([
            'summary' => $summary,
            'description' => $description,
            'start' => [
                'dateTime' => $start->toRfc3339String(),
                // 'timeZone' => 'UTC',//$calendar->getTimeZone(),//
            ],
            'end' => [
                'dateTime' => $start->addHour()->toRfc3339String(),
                // 'timeZone' => 'UTC',//$calendar->getTimeZone(),//
            ],
            'attendees' => [
                ['email' => Auth::user()->google_name],
                ['email' => $member->email], //array('email' => 'dimgem@gmail.com'),//
            ],
            'reminders' => [
                'useDefault' => true,
            ],
        ]);
        //$event->setLocation('The Neighbourhood');
        $event->setConferenceData(self::getconferenceData());

        //$EventCreator = new \Google_Service_Calendar_EventCreator();
        //$EventCreator->setDisplayName( 'Training Team' );
        //$EventCreator->setEmail( Auth::user()->google_name );
        //$event->setCreator($EventCreator );

        return self::add_event($calendar_id, $event);
    }

    public static function get_event($calendar_id, $event_id)
    {
        $service = app(Google::class)->connectUser(Auth::user())->service('Calendar');
        $event = $service->events->get($calendar_id, $event_id);

        return $event;
    }

    public static function get_calendar($calendar_id)
    {
        $service = app(Google::class)->connectUser(Auth::user())->service('Calendar');
        $calendar = $service->calendars->get($calendar_id);

        return $calendar;
    }

    public static function getconferenceData(): Google_Service_Calendar_ConferenceData
    {
        $conferenceData = new Google_Service_Calendar_ConferenceData([
            'createRequest' => new Google_Service_Calendar_CreateConferenceRequest([
                'requestId' => Str::random(10),
                'conferenceSolutionKey' => new Google_Service_Calendar_ConferenceSolutionKey([
                    'type' => 'hangoutsMeet',
                ]),
            ]),
        ]);

        return $conferenceData;
    }

    //write function to create google calendar event
    public static function update_event($calendar_id, $event_id, $summary, $start, $end, $description)
    {
        $event = self::get_event($calendar_id, $event_id);
        $event->setSummary($summary);
        $event->setDescription($description);
        $event->getStart()->setDateTime($start);
        $event->getStart()->setTimeZone('America/Chicago');
        $event->getEnd()->setDateTime($end);
        $event->getEnd()->setTimeZone('America/Chicago');

        return self::add_event($calendar_id, $event);
    }

    //write function to delete google calendar event
    public static function delete_event($calendar_id, $event_id)
    {
        $service = app(Google::class)->connectUsing(Auth::user()->google_token, Auth::user()->google_refresh_token)->service('Calendar');
        $event = $service->events->delete($calendar_id, $event_id);

        return $event;
    }

    //write function to get google calendar events
    public static function get_events($calendar_id, $timeMin, $timeMax)
    {
        $service = app(Google::class)->connectUsing(Auth::user()->google_token, Auth::user()->google_refresh_token)->service('Calendar');
        $optParams = [
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => $timeMin,
            'timeMax' => $timeMax,
        ];
        $results = $service->events->listEvents($calendar_id, $optParams);

        return $results->getItems();
    }
}
