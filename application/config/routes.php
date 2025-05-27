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

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['default_controller'] = 'Welcome/index';


//AUTH ROUTES
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
$route['student/evaluation-form/list'] = 'StudentController/evaluation_form_list';
$route['student/evaluation-forms'] = 'StudentController/evaluation_forms';
$route['student/evaluation-form-questions/(:num)'] = 'StudentController/evaluation_form_questions/$1';
$route['student/evaluation/submit']['post'] = 'StudentController/submit_form';
$route['student/evaluation-form-submit/(:num)'] = 'StudentController/submit/$1';
$route['student/evaluation-answers/(:num)'] = 'StudentController/view_evaluation_answers/$1';

// PROFILE SETTINGS
$route['student/profile-settings'] = 'StudentController/profile_settings';
$route['student/profile/update-profile-pic'] = 'StudentController/update_profile_pic';
$route['student/profile/update-profile'] = 'StudentController/update_profile';
$route['student/profile/update_password'] = 'StudentController/update_password';
$route['student/profile/get_qr_code_by_student'] = 'StudentController/get_qr_code_by_student';

//STUDENT RECEIPTS
$route['student/receipts'] = 'StudentController/receipts_page';

$route['student/about'] = 'StudentController/about';

//ADMIN ROUTES
$route['admin/dashboard'] = 'AdminController/admin_dashboard';

// ATTENDANCE MONITORING
$route['admin/list-activities-attendance'] = 'AdminController/list_activities_attendance';
$route['admin/list-attendees/(:num)'] = 'AdminController/list_attendees/$1';
// ~ TAKING OF ATTENDANCE~
$route['admin/activity/scan-qr/time-in/(:num)/(:num)'] = 'AdminController/time_in/$1/$2';
$route['admin/activity/update-fine/time-in'] = 'AdminController/impose_fines_timein';
$route['admin/activity/scan-qr/time-out/(:num)/(:num)'] = 'AdminController/time_out/$1/$2';
$route['admin/activity/update-fine/time-out'] = 'AdminController/impose_fines_timeout';
$route['admin/activity/face-recognition/(:num)'] = 'AdminController/face_recognition/$1';
$route['admin/attendance/get-faces'] = 'AdminController/getFaces';
$route['admin/attendance/detect_timein'] = 'AdminController/scanUnified_timein';
$route['admin/attendance/detect_timeout'] = 'AdminController/scanUnified_timeout';


//FINES MONITORING
// $route['admin/list-activities-fines'] = 'AdminController/list_activities_fines';
// $route['admin/list-department-fines/(:num)'] = 'AdminController/list_department_fines/$1';
//$route['admin/list-fines/(:num)/(:num)'] = 'AdminController/list_fines/$1/$2';
$route['admin/list-fines'] = 'AdminController/list_fines';
$route['admin/fines-payment/confirm'] = 'AdminController/confirm_payment';
$route['admin/fines/update_status']['post'] = 'AdminController/update_status';


// CREATE ACTIVITY
$route['admin/create-activity'] = 'AdminController/create_activity'; // VIEW CREATE ACTIVITY PAGE
$route['admin/create-activity/add']['post'] = 'AdminController/save_activity'; // SAVING ACTIVITY TO DATABASE


$route['admin/edit-activity/(:num)'] = 'AdminController/edit_activity/$1'; // VIEW EDIT ACTIVITY PAGE
$route['admin/edit-activity/update/(:num)'] = 'AdminController/update_activity/$1'; // UPDATING ACTIVITY TO DATABASE
$route['admin/delete-schedule/(:num)'] = 'AdminController/delete_schedule/$1';
$route['admin/list-of-activity'] = 'AdminController/list_activity'; // List of activity
$route['admin/activity-details/(:num)'] = 'AdminController/activity_details/$1'; // Activity Details
$route['admin/activity-details/activity-share']['post'] = 'AdminController/share_activity'; // Sharing activity to community
$route['admin/unshare-activity'] = 'AdminController/unshare_activity'; //UNSHARE ACTIVITY
$route['admin/activity/registration'] = 'AdminController/validate_registrations';
$route['admin/cash-payment/submit'] = 'AdminController/save_cash_payment';
$route['admin/view-edit-logs/(:num)'] = 'AdminController/get_edit_logs/$1';

$route['admin/list-activity-evaluation'] = 'AdminController/list_activity_evaluation';
$route['admin/create-evaluation-form'] = 'AdminController/create_evaluationform';
$route['admin/create-evaluation-form/create']['post'] = 'AdminController/create_eval';
$route['admin/edit-evaluation-form/(:num)'] = 'AdminController/edit_evaluationform/$1';
$route['admin/edit-evaluation-form/update/(:num)'] = 'AdminController/update_eval/$1';
$route['admin/view-evaluation-form/(:num)'] = 'AdminController/view_evaluationform/$1';
$route['admin/list-evaluation-responses/(:num)'] = 'AdminController/list_evaluation_responses/$1';
$route['admin/evaluation-statistic/(:num)'] = 'AdminController/evaluation_statistic/$1';

// <==== EXCUSE APPLICATION ======>
$route['admin/activity-list'] = 'AdminController/list_activity_excuse'; // List of activity
$route['admin/list-of-excuse-letter/(:num)'] = 'AdminController/list_excuse_letter/$1'; // List of Excuse Application per Activity
$route['admin/review-excuse-letter/(:num)'] = 'AdminController/review_excuse_letter/$1'; // Excuse Application
$route['admin/review-excuse-letter/update']['POST'] = 'AdminController/updateApprovalStatus'; // Remarks of the application

// COMMUNITY SECTION
$route['admin/community'] = 'AdminController/community';
$route['admin/community/like-post/(:num)'] = 'AdminController/like_post/$1'; // Route for liking a post
$route['admin/view_likes/(:num)'] = 'AdminController/view_likes/$1'; // Route for the viewing of user who like the post
$route['admin/community/unlike-post/(:num)'] = 'AdminController/unlike_post/$1'; // Route for unliking a post
$route['admin/community/add-comment']['post'] = 'AdminController/add_comment'; // Route for adding of comment
$route['admin/community/add-post']['post'] = 'AdminController/add_post'; // Route for adding of post
$route['admin/community/share-activity']['post'] = 'AdminController/share_activity'; // Route for sharing activity
$route['admin/community/delete-post']['post'] = 'AdminController/delete_post'; // Route for deleting post

//PROFILE SETTINGS
$route['admin/profile-settings'] = 'AdminController/profile_settings';
$route['admin/profile/update-profile-pic'] = 'AdminController/update_profile_pic';
$route['admin/profile/update-profile'] = 'AdminController/update_profile';
$route['admin/profile/update_password'] = 'AdminController/update_password';
$route['admin/profile/get_qr_code_by_student'] = 'AdminController/get_qr_code_by_student';

// MANAGE OFFICER AND PRIVILEGE
$route['admin/manage-officers'] = 'AdminController/manage_officers';
$route['admin/manage-officers-department/(:num)'] = 'AdminController/list_officers_dept/$1';
$route['admin/manage-officers-department/update_privileges'] = 'AdminController/update_privileges_dept';
$route['admin/manage-officers-department/delete-officer'] = 'AdminController/delete_officer_dept';
$route['admin/manage-officers-organization/(:num)'] = 'AdminController/list_officers_org/$1';
$route['admin/manage-officers-organization/update_privileges'] = 'AdminController/update_privileges_org';
$route['admin/manage-officers-organization/delete-officer'] = 'AdminController/delete_officer_org';

// GENERAL SETTINGS
$route['admin/general-settings'] = 'AdminController/general_settings';
$route['admin/import-students'] = 'AdminController/import_list';
$route['admin/import-department-officers'] = 'AdminController/import_list_dept';
$route['admin/import-organization-officers'] = 'AdminController/import_list_org';
$route['admin/import-exempted-students'] = 'AdminController/import_exempted_students';
$route['admin/generate_bulk_qr'] = 'AdminController/generate_bulk_qr';
$route['admin/save-organization'] = 'AdminController/save_organization';
$route['admin/get_organizations'] = 'AdminController/get_organizations';
$route['admin/delete_organization/(:num)'] = 'AdminController/delete_organization/$1';
$route['admin/update-organization'] = 'AdminController/update_organization';


$route['admin/verify-receipt-page'] = 'AdminController/verify_receipt_page';
$route['admin/verify-receipt'] = 'AdminController/verify_receipt';

$route['admin/about'] = 'AdminController/about';

// OFFICER ROUTES
$route['officer/dashboard'] = 'OfficerController/officer_dashboard';

// ATTENDANCE MONITORING
$route['officer/list-activities-attendance'] = 'OfficerController/list_activities_attendance';
$route['officer/list-attendees/(:num)'] = 'OfficerController/list_attendees/$1';
// ~ TAKING OF ATTENDANCE~
$route['officer/activity/scan-qr/time-in/(:num)/(:num)'] = 'OfficerController/time_in/$1/$2';
$route['officer/activity/update-fine/time-in'] = 'OfficerController/impose_fines_timein';
$route['officer/activity/scan-qr/time-out/(:num)/(:num)'] = 'OfficerController/time_out/$1/$2';
$route['officer/activity/update-fine/time-out'] = 'OfficerController/impose_fines_timeout';
$route['officer/activity/face-recognition/(:num)'] = 'OfficerController/face_recognition/$1';
$route['officer/attendance/get-faces'] = 'OfficerController/getFaces';
$route['officer/attendance/detect_timein'] = 'OfficerController/scanUnified_timein';
$route['officer/attendance/detect_timeout'] = 'OfficerController/scanUnified_timeout';


//FINES MONITORING
// $route['admin/list-activities-fines'] = 'AdminController/list_activities_fines';
// $route['admin/list-department-fines/(:num)'] = 'AdminController/list_department_fines/$1';
//$route['admin/list-fines/(:num)/(:num)'] = 'AdminController/list_fines/$1/$2';
$route['officer/list-fines'] = 'OfficerController/list_fines';
$route['officer/fines-payment/confirm'] = 'OfficerController/confirm_payment';
$route['officer/fines/update_status']['post'] = 'OfficerController/update_status';


//<======= CREATION ACTIVITY ======>
$route['officer/create-activity'] = 'OfficerController/create_activity'; // VIEW CREATE ACTIVITY PAGE
$route['officer/create-activity/add']['post'] = 'OfficerController/save_activity'; // SAVING ACTIVITY TO DATABASE
$route['officer/edit-activity/(:num)'] = 'OfficerController/edit_activity/$1'; // VIEW EDIT ACTIVITY PAGE
$route['officer/edit-activity/update/(:num)'] = 'OfficerController/update_activity/$1'; // UPDATING ACTIVITY TO DATABASE
$route['officer/delete-schedule/(:num)'] = 'OfficerController/delete_schedule/$1';
$route['officer/list-of-activity'] = 'OfficerController/list_activity'; // List of activity
$route['officer/activity-details/(:num)'] = 'OfficerController/activity_details/$1'; // Activity Details
$route['officer/activity-details/activity-share']['post'] = 'OfficerController/share_activity'; // Sharing activity to community
$route['officer/unshare-activity'] = 'OfficerController/unshare_activity'; //UNSHARE ACTIVITY
$route['officer/activity/registration'] = 'OfficerController/validate_registrations';
$route['officer/cash-payment/submit'] = 'OfficerController/save_cash_payment';
$route['officer/view-edit-logs/(:num)'] = 'OfficerController/get_edit_logs/$1';

$route['officer/list-activity-evaluation'] = 'OfficerController/list_activity_evaluation';
$route['officer/create-evaluation-form'] = 'OfficerController/create_evaluationform';
$route['officer/create-evaluation-form/create']['post'] = 'OfficerController/create_eval';
$route['officer/edit-evaluation-form/(:num)'] = 'OfficerController/edit_evaluationform/$1';
$route['officer/edit-evaluation-form/update/(:num)'] = 'OfficerController/update_eval/$1';
$route['officer/view-evaluation-form/(:num)'] = 'OfficerController/view_evaluationform/$1';
$route['officer/list-evaluation-responses/(:num)'] = 'OfficerController/list_evaluation_responses/$1';
$route['officer/evaluation-statistic/(:num)'] = 'OfficerController/evaluation_statistic/$1';

// <==== EXCUSE APPLICATION ======>
$route['officer/activity-list'] = 'OfficerController/list_activity_excuse'; // List of activity
$route['officer/list-of-excuse-letter/(:num)'] = 'OfficerController/list_excuse_letter/$1'; // List of Excuse Application per Activity
$route['officer/review-excuse-letter/(:num)'] = 'OfficerController/review_excuse_letter/$1'; // Excuse Application
$route['officer/review-excuse-letter/update']['POST'] = 'OfficerController/updateApprovalStatus'; // Remarks of the application

// <===== COMMUNITY SECTION =======>
$route['officer/community'] = 'OfficerController/community';
//$route['admin/community/posts']['post'] = 'v/fetch_more_posts';
$route['officer/community/like-post/(:num)'] = 'OfficerController/like_post/$1'; // Route for liking a post
$route['officer/view_likes/(:num)'] = 'OfficerController/view_likes/$1'; // Route for the viewing of user who like the post
$route['officer/community/unlike-post/(:num)'] = 'OfficerController/unlike_post/$1'; // Route for unliking a post
$route['officer/community/add-comment']['post'] = 'OfficerController/add_comment'; // Route for adding of comment
$route['officer/community/add-post']['post'] = 'OfficerController/add_post'; // Route for adding of post
$route['officer/community/share-activity']['post'] = 'OfficerController/share'; // Route for sharing activity
$route['officer/community/delete-post']['post'] = 'OfficerController/delete_post'; // Route for deleting post

// PROFILE SETTINGS ========>
//PROFILE UPDATES
$route['officer/profile-settings'] = 'OfficerController/profile_settings';
$route['officer/profile/update-profile-pic'] = 'OfficerController/update_profile_pic';
$route['officer/profile/update-profile'] = 'OfficerController/update_profile';
$route['officer/profile/update_password'] = 'OfficerController/update_password';
$route['officer/profile/get_qr_code_by_student'] = 'OfficerController/get_qr_code_by_student';

$route['officer/manage-officers'] = 'OfficerController/list_officers';
$route['officer/manage-officers/update_privileges'] = 'OfficerController/update_privileges';
$route['officer/manage-officers/delete-officer'] = 'OfficerController/delete_officer_dept';

$route['officer/general-settings'] = 'OfficerController/general_settings';
$route['officer/import-students'] = 'OfficerController/import_list';
$route['officer/generate_bulk_qr'] = 'OfficerController/generate_bulk_qr';



$route['officer/verify-receipt-page'] = 'OfficerController/verify_receipt_page';
$route['officer/verify-receipt'] = 'OfficerController/verify_receipt';

$route['officer/about'] = 'OfficerController/about';



//NOTIFICATIONS ROUTES
