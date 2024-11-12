Auth Routes
These routes manage user authentication and session status.

POST /login
Description: Logs the user in and returns an authentication token.
Controller Method: AuthController@login

POST /check_if_logged_in
Description: Checks if the user is logged in.
Controller Method: AuthController@check_if_logged_in

ANY /logout (Requires auth:sanctum, api_employee middleware)
Description: Logs the user out, invalidating the token.
Controller Method: AuthController@logout

Leave Management Routes
These routes allow managing leave requests.

GET /leave
Description: Retrieves a list of leave records.
Controller Method: ApiLeaveController@index

GET /leave/{id}
Description: Retrieves details of a specific leave request.
Controller Method: ApiLeaveController@show

POST /leave
Description: Creates a new leave request.
Controller Method: ApiLeaveController@store

POST /leave/{id}/edit
Description: Updates an existing leave request.
Controller Method: ApiLeaveController@update

POST /leave/{id}/delete
Description: Deletes a specific leave request.
Controller Method: ApiLeaveController@destroy

Clock In/Out Routes
These routes are used for clocking in and out.

GET /clock-in-out
Description: Retrieves clock-in and clock-out records.
Controller Method: ApiClockInOutController@index

POST /clock-in-out
Description: Submits a clock-in or clock-out entry.
Controller Method: ApiClockInOutController@store

Directory and Team Routes
Provides access to directory information and team details.

GET /directory
Description: Retrieves a list of directory entries.
Controller Method: ApiDirectoryController@index

GET /team
Description: Retrieves information on team members.
Controller Method: ApiTeamController@index

Announcements Routes
GET /announcements
Description: Retrieves announcements for the user.
Controller Method: ApiAnnouncementController@index
Request Status Routes
These routes manage request statuses and messages.

GET /request-status
Description: Loads the request status records.
Controller Method: ApiRequestStatusController@loadRecords

POST /request-status/first-time
Description: Checks if the request is a first-time request.
Controller Method: ApiRequestStatusController@isFirstTime

POST /request-status/make-seen
Description: Marks a request status as seen.
Controller Method: ApiRequestStatusController@makeSeen

POST /request-status/send-message
Description: Sends a message in the request status context.
Controller Method: ApiRequestStatusController@sendMessage

GET /request-status/download/{messageId}/{attachmentId}
Description: Downloads an attachment associated with a specific message.
Controller Method: ApiRequestStatusController@download