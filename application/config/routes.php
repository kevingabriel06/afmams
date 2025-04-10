<?php
defined('BASEPATH') or exit('No direct script access allowed');

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
$route['student/home/like-post/(:num)'] = 'StudentController/like_post/$1'; // Route for liking a post
$route['student/view_likes/(:num)'] = 'StudentController/view_likes/$1'; // Route for the viewing of user who like the post
$route['student/home/unlike-post/(:num)'] = 'StudentController/unlike_post/$1'; // Route for unliking a post
$route['student/home/add-comment']['post'] = 'StudentController/add_comment'; // Route for adding of comment
$route['student/register']['post'] = 'StudentController/register';
$route['student/attend']['post'] = 'StudentController/attend_free_event';
$route['student/cancel']['post'] = 'StudentController/cancelAttendance';


//count the number who clicked attend

$route['student/express-interest'] = 'StudentController/express_interest';

// ATTENDANCE
$route['student/attendance-history'] = 'StudentController/attendance_history';

// FINES
$route['student/summary-fines'] = 'StudentController/summary_fines';


// ACTIVITY
$route['student/list-activity'] = 'StudentController/list_activity';
$route['student/activity-details/(:any)'] = 'StudentController/activity_details/$1';

// EXCUSE APPLICATION
$route['student/excuse-application/list'] = 'StudentController/excuse_application_list';
$route['student/excuse-application/submit'] = 'StudentController/submit_application';
$route['student/excuse-application'] = 'StudentController/excuse_application';

// EVALUATION FORM
$route['student/evaluation-form'] = 'StudentController/evaluation_form';
$route['student/evaluation-form-questions/(:num)'] = 'StudentController/evaluation_form_questions/$1';
$route['student/evaluation-form-submit/(:num)'] = 'StudentController/submit/$1';
$route['student/evaluation-answers/(:num)'] = 'StudentController/view_evaluation_answers/$1';

$route['student/profile-settings/(:any)'] = 'StudentController/profile_settings/$1';
$route['student/update_profile_pic'] = 'StudentController/update_profile_pic';
//update profile details
$route['student/update-profile/(:any)'] = 'StudentController/update_profile/$1';

$route['about'] = 'StudentController/about_page';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

//ADMIN ROUTES
$route['admin/dashboard'] = 'AdminController/admin_dashboard';

// ATTENDANCE MONITORING =======>
$route['admin/list-activities-attendance'] = 'AdminController/list_activities_attendance';
$route['admin/list-department/(:num)'] = 'AdminController/list_department/$1';
$route['admin/list-attendees/(:num)/(:num)'] = 'AdminController/list_attendees/$1/$2';
$route['admin/activity/scan-qr/time-in/(:num)'] = 'AdminController/scanning_qr/$1';
$route['admin/activity/scan-qr/time-out/(:num)'] = 'AdminController/scanning_qr/$1';
$route['admin/activity/face-recognition/(:num)'] = 'AdminController/face_recognition/$1';
$route['admin/activity/scan-qr-code'] = 'AdminController/scan';
$route['admin/attendance/get-faces'] = 'AdminController/getFaces';
$route['admin/attendance/detect'] = 'AdminController/updateAttendance';


//FINES MONITORING
// $route['admin/list-activities-fines'] = 'AdminController/list_activities_fines';
// $route['admin/list-department-fines/(:num)'] = 'AdminController/list_department_fines/$1';
//$route['admin/list-fines/(:num)/(:num)'] = 'AdminController/list_fines/$1/$2';
$route['admin/list-fines'] = 'AdminController/list_fines';
$route['admin/fines-payment/confirm'] = 'AdminController/confirm_payment';
$route['admin/fines/update_status']['post'] = 'AdminController/update_status';


//<======= CREATION ACTIVITY ======>
$route['admin/create-activity'] = 'AdminController/create_activity'; // VIEW CREATE ACTIVITY PAGE
$route['admin/create-activity/add']['post'] = 'AdminController/save_activity'; // SAVING ACTIVITY TO DATABASE
$route['admin/edit-activity/(:num)'] = 'AdminController/edit_activity/$1'; // VIEW EDIT ACTIVITY PAGE
$route['admin/edit-activity/update/(:num)'] = 'AdminController/update_activity/$1'; // UPDATING ACTIVITY TO DATABASE
$route['admin/delete-schedule/(:num)'] = 'AdminController/delete_schedule/$1';
$route['admin/list-of-activity'] = 'AdminController/list_activity'; // List of activity
$route['admin/activity-details/(:num)'] = 'AdminController/activity_details/$1'; // Activity Details
$route['admin/activity-details/activity-share']['post'] = 'AdminController/share_activity'; // Sharing activity to community

$route['admin/create-evaluation-form'] = 'AdminController/create_evaluationform';
$route['admin/create-evaluation-form/create']['post'] = 'AdminController/create_eval';
$route['admin/edit-evaluation-form/(:num)'] = 'AdminController/edit_evaluationform/$1';
$route['admin/edit-evaluation-form/update/(:num)'] = 'AdminController/update_eval/$1';
$route['admin/view-evaluation-form/(:num)'] = 'AdminController/view_evaluationform/$1';
$route['admin/list-activity-evaluation'] = 'AdminController/list_activity_evaluation';
$route['admin/list-evaluation-answers/(:num)'] = 'AdminController/list_evaluation_answers/$1';
$route['admin/list-evaluation-answers/view_response/(:num)'] = 'AdminController/view_response/$1';

// <==== EXCUSE APPLICATION ======>
$route['admin/activity-list'] = 'AdminController/list_activity_excuse'; // List of activity
$route['admin/list-of-excuse-letter/(:num)'] = 'AdminController/list_excuse_letter/$1'; // List of Excuse Application per Activity
$route['admin/review-excuse-letter/(:num)'] = 'AdminController/review_excuse_letter/$1'; // Excuse Application
$route['admin/review-excuse-letter/update']['POST'] = 'AdminController/updateApprovalStatus'; // Remarks of the application

// <===== COMMUNITY SECTION =======>
$route['admin/community'] = 'AdminController/community';
//$route['admin/community/posts']['post'] = 'AdminController/fetch_more_posts';
$route['admin/community/like-post/(:num)'] = 'AdminController/like_post/$1'; // Route for liking a post
$route['admin/view_likes/(:num)'] = 'AdminController/view_likes/$1'; // Route for the viewing of user who like the post
$route['admin/community/unlike-post/(:num)'] = 'AdminController/unlike_post/$1'; // Route for unliking a post
$route['admin/community/add-comment']['post'] = 'AdminController/add_comment'; // Route for adding of comment
$route['admin/community/add-post']['post'] = 'AdminController/add_post'; // Route for adding of post
$route['admin/community/share-activity']['post'] = 'AdminController/share'; // Route for sharing activity
$route['admin/community/delete-post']['post'] = 'AdminController/delete_post'; // Route for deleting post

// PROFILE SETTINGS ========>
//PROFILE UPDATES
$route['admin/profile-settings/(:any)'] = 'AdminController/profile_settings/$1';
$route['admin/update_profile_pic'] = 'AdminController/update_profile_pic';
//update profile details
$route['admin/update-profile/(:any)'] = 'AdminController/update_profile/$1';
$route['admin/manage-officers'] = 'AdminController/manage_officers';
$route['admin/manage-officers-department/(:num)'] = 'AdminController/list_officers_dept/$1';
$route['admin/manage-officers-department/update_status']['post'] = 'AdminController/update_status_dept';
$route['admin/manage-officers-organization/(:num)'] = 'AdminController/list_officers_org/$1';
$route['admin/manage-officers-organization/update_status']['post'] = 'AdminController/update_status_org';
