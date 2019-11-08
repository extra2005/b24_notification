<?
CModule::IncludeModule('calendar');

AddEventHandler("main", "OnPageStart", Array("CalendarEditNotify","OnPageStartHandler"));
AddEventHandler("calendar", "OnAfterCalendarEntryUpdate", Array("CalendarEditNotify","OnCalendarEntryUpdateHandler"));

class CalendarEditNotify{
	private static $_currentEvent = array();
	
	static public function OnPageStartHandler(){
			if($_REQUEST['markAction']=="editEvent" && $_REQUEST['id']>0)
				self::$_currentEvent = CCalendarEvent::GetList(array('arFilter' => array("PARENT_ID" => (int)$_REQUEST['id'])));
	}
	
	static public function OnCalendarEntryUpdateHandler($id,$entryFields){
		$fromTo = CCalendarEvent::GetEventFromToForUser($entryFields, $entryFields['OWNER_ID']);
		if(self::$_currentEvent['NAME']!=$entryFields['NAME'] || self::$_currentEvent['DATE_FROM']!=$fromTo['DATE_FROM'] || self::$_currentEvent['DATE_TO']!=$fromTo['DATE_TO']){
			CCalendarNotify::ClearNotifications($entryFields['PARENT_ID']);
			CCalendarNotify::Send(array(
				'mode' => 'invite',
				'name' => $entryFields['NAME'],
				'from' => $fromTo['DATE_FROM'],
				'to' => $fromTo['DATE_TO'],
				'location' => CCalendar::GetTextLocation($entryFields["LOCATION"]),
				"guestId" => $entryFields['OWNER_ID'],
				"eventId" => $id,
				"userId" => $entryFields['~MEETING']['MEETING_CREATOR'],));
		}
	}
}