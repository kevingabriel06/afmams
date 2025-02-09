<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
//AUTH ROUTES
$route['default_controller'] = 'welcome';
$route['login'] = 'AuthController/login';
$route['logout'] = 'AuthController/logout';


$route['admin/dashboard/(:any)'] = 'AdminController/admin_dashboard/$1';
$route['officer/dashboard/(:any)'] = 'OfficerController/officer_dashboard/$1';


//STUDENT ROUTES
$route['student/home'] = 'StudentController/student_dashboard';
$route['student/excuse-application'] = 'StudentController/excuse_application';
$route['student/attendance-history'] = 'StudentController/attendance_history';
$route['student/list-activity'] = 'StudentController/list_activity';
$route['student/summary-fines'] = 'StudentController/summary_fines';
$route['student/evaluation-form'] = 'StudentController/evaluation_form';
$route['student/profile-settings'] = 'StudentController/profile_settings';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


//ADMIN ROUTES
$route['admin/dashboard'] = 'AdminController/admin_dashboard';

// ATTENDANCE MONITORING =======>
$route['admin/list-activities-attendance'] = 'AdminController/list_activities_attendance';
$route['admin/list-attendees/(:num)'] = 'AdminController/list_attendees/$1';
$route['admin/activity/scan-qr/(:num)'] = 'AdminController/scanning_qr/$1';
$route['admin/activity/scan-qr-code'] = 'AdminController/scan';
$route['admin/attendance/get-faces'] = 'AdminController/getFaces';
$route['admin/attendance/detect'] = 'AdminController/updateAttendance';


//FINES MONITORING
$route['admin/list-activities-fines'] = 'AdminController/list_activities_fines';
$route['admin/list-fines/(:num)'] = 'AdminController/list_fines/$1';


//ACTIVITY MANAGEMENT======>
$route['admin/create-activity'] ='AdminController/create_activity'; // VIEW CREATE ACTIVITY PAGE
$route['admin/create-activity/add']['POST'] = 'AdminController/save_activity' ; // SAVING ACTIVITY TO DATABASE
$route['admin/edit-activity/(:num)'] ='AdminController/edit_activity/$1'; // VIEW EDIT ACTIVITY PAGE
$route['admin/edit-activity/update']['POST'] ='AdminController/update_activity'; // UPDATING ACTIVITY TO DATABASE
$route['admin/list-of-activity'] = 'AdminController/list_activity';
$route['admin/activity-details/(:num)'] = 'AdminController/activity_details/$1';
$route['admin/activity-details/activity-share']['post'] = 'AdminController/share_activity';

$route['admin/create-evaluation-form'] = 'AdminController/create_evaluationform';
$route['admin/create-evaluation-form/create']['post'] = 'AdminController/create';

//=======> EXCUSE PART
$route['admin/activity-list'] = 'AdminController/list_activity_excuse';
$route['admin/list-of-excuse-letter/(:num)'] = 'AdminController/list_excuse_letter/$1';
$route['admin/review-excuse-letter/(:num)'] = 'AdminController/review_excuse_letter/$1';
$route['admin/review-excuse-letter/update']['POST'] = 'AdminController/updateApprovalStatus';

// ======> COMMUNITY SECTION
$route['admin/community'] = 'AdminController/community';

$route['admin/community/like-post/(:num)'] = 'AdminController/like_post/$1'; // Route for liking a post
$route['admin/community/unlike-post/(:num)'] = 'AdminController/unlike_post/$1'; // Route for unliking a post
$route['admin/community/add-comment']['post'] = 'AdminController/add_comment'; // Route for adding of comment
$route['admin/community/add-post']['post'] = 'AdminController/add_post';
$route['admin/community/share-activity']['post'] = 'AdminController/share';
$route['admin/community/delete-post']['post'] = 'AdminController/delete_post';

// PROFILE SETTINGS ========>
$route['admin/profile-settings'] = 'AdminController/profile_settings';
$route['admin/manage-officers'] = 'AdminController/manage_officers';

