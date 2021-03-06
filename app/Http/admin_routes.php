<?php

/* ================== Homepage ================== */
Route::get('/', 'LA\DashboardController@index');
Route::get('/home', 'HomeController@index');
Route::auth();

/* ================== Access Uploaded Files ================== */
Route::get('files/{hash}/{name}', 'LA\UploadsController@get_file');

/*
  |--------------------------------------------------------------------------
  | Admin Application Routes
  |--------------------------------------------------------------------------
 */

$as = "";
if (\Dwij\Laraadmin\Helpers\LAHelper::laravel_ver() == 5.3) {
    $as = config('laraadmin.adminRoute') . '.';

    // Routes for Laravel 5.3
    Route::get('/logout', 'Auth\LoginController@logout');
}

Route::group(['as' => $as, 'middleware' => ['auth', 'permission:ADMIN_PANEL', 'prevent-back-history']], function () {

    /* ================== Dashboard ================== */

    Route::get(config('laraadmin.adminRoute'), 'LA\DashboardController@index');
    Route::get(config('laraadmin.adminRoute') . '/dashboard', 'LA\DashboardController@index');

    /* ================== Users ================== */
    Route::resource(config('laraadmin.adminRoute') . '/users', 'LA\UsersController');
    Route::get(config('laraadmin.adminRoute') . '/user_dt_ajax', 'LA\UsersController@dtajax');

    /* ================== Uploads ================== */
    Route::resource(config('laraadmin.adminRoute') . '/uploads', 'LA\UploadsController');
    Route::post(config('laraadmin.adminRoute') . '/upload_files', 'LA\UploadsController@upload_files');
    Route::get(config('laraadmin.adminRoute') . '/uploaded_files', 'LA\UploadsController@uploaded_files');
    Route::post(config('laraadmin.adminRoute') . '/uploads_update_caption', 'LA\UploadsController@update_caption');
    Route::post(config('laraadmin.adminRoute') . '/uploads_update_filename', 'LA\UploadsController@update_filename');
    Route::post(config('laraadmin.adminRoute') . '/uploads_update_public', 'LA\UploadsController@update_public');
    Route::post(config('laraadmin.adminRoute') . '/uploads_delete_file', 'LA\UploadsController@delete_file');

    /* ================== Roles ================== */
    Route::resource(config('laraadmin.adminRoute') . '/roles', 'LA\RolesController');
    Route::get(config('laraadmin.adminRoute') . '/role_dt_ajax', 'LA\RolesController@dtajax');
    Route::post(config('laraadmin.adminRoute') . '/save_module_role_permissions/{id}', 'LA\RolesController@save_module_role_permissions');

    /* ================== Permissions ================== */
    Route::resource(config('laraadmin.adminRoute') . '/permissions', 'LA\PermissionsController');
    Route::get(config('laraadmin.adminRoute') . '/permission_dt_ajax', 'LA\PermissionsController@dtajax');
    Route::post(config('laraadmin.adminRoute') . '/save_permissions/{id}', 'LA\PermissionsController@save_permissions');

    /* ================== Departments ================== */
    Route::resource(config('laraadmin.adminRoute') . '/departments', 'LA\DepartmentsController');
    Route::get(config('laraadmin.adminRoute') . '/department_dt_ajax', 'LA\DepartmentsController@dtajax');

    /* ================== Employees ================== */
    Route::resource(config('laraadmin.adminRoute') . '/employees', 'LA\EmployeesController');
    Route::post(config('laraadmin.adminRoute') . '/employee_dt_ajax', 'LA\EmployeesController@dtajax');
    Route::post(config('laraadmin.adminRoute') . '/change_password/{id}', 'LA\EmployeesController@change_password');

    /* ================== Organizations ================== */
    Route::resource(config('laraadmin.adminRoute') . '/organizations', 'LA\OrganizationsController');
    Route::get(config('laraadmin.adminRoute') . '/organization_dt_ajax', 'LA\OrganizationsController@dtajax');

    /* ================== Backups ================== */
    Route::resource(config('laraadmin.adminRoute') . '/backups', 'LA\BackupsController');
    Route::get(config('laraadmin.adminRoute') . '/backup_dt_ajax', 'LA\BackupsController@dtajax');
    Route::post(config('laraadmin.adminRoute') . '/create_backup_ajax', 'LA\BackupsController@create_backup_ajax');
    Route::get(config('laraadmin.adminRoute') . '/downloadBackup/{id}', 'LA\BackupsController@downloadBackup');

    /* ================== Projects ================== */
    Route::resource(config('laraadmin.adminRoute') . '/projects', 'LA\ProjectsController');
    Route::get(config('laraadmin.adminRoute') . '/project_dt_ajax', 'LA\ProjectsController@dtajax');
    Route::post(config('laraadmin.adminRoute') . '/projectList', 'LA\ProjectsController@ajaxProjectList');
    Route::get(config('laraadmin.adminRoute') . "/project/graphicalRepresentation", 'LA\ProjectsController@graphicalRepresentationSetup');
    Route::post(config('laraadmin.adminRoute') . "/project/sendMailWithGraphs", 'LA\ProjectsController@ajaxSendMailWithGraphs');

    /* ================== Tasks ================== */
    Route::resource(config('laraadmin.adminRoute') . '/tasks', 'LA\TasksController');
    Route::get(config('laraadmin.adminRoute') . '/task_dt_ajax', 'LA\TasksController@dtajax');

    /* ================== Leads ================== */
    Route::resource(config('laraadmin.adminRoute') . '/leads', 'LA\LeadsController');
    Route::get(config('laraadmin.adminRoute') . '/lead_dt_ajax', 'LA\LeadsController@dtajax');

    /* ================== Managers ================== */
    Route::resource(config('laraadmin.adminRoute') . '/managers', 'LA\ManagersController');
    Route::get(config('laraadmin.adminRoute') . '/manager_dt_ajax', 'LA\ManagersController@dtajax');

    /* ================== Timesheets ================== */
    Route::resource(config('laraadmin.adminRoute') . '/timesheets', 'LA\TimesheetsController');
    Route::get(config('laraadmin.adminRoute') . '/timesheet_dt_ajax', 'LA\TimesheetsController@dtajax');
    Route::post("/sendEmailToLeadsAndManagers", 'LA\TimesheetsController@sendEmailToLeadsAndManagers');
    Route::post("/hoursWorked", 'LA\TimesheetsController@ajaxHoursWorked');
    Route::post("/datesMailPending", 'LA\TimesheetsController@ajaxDatesMailPending');
    Route::post("/exportTimeSheetToAuthority", 'LA\TimesheetsController@ajaxExportTimeSheetToAuthority');
    Route::get(config('laraadmin.adminRoute') . "/downloadTimesheet", 'LA\TimesheetsController@downloadTimesheet');
    Route::get(config('laraadmin.adminRoute') . "/timesheet/teamMembers", 'LA\TimesheetsController@teamMemberSheet');
    Route::get(config('laraadmin.adminRoute') . "/timesheet/sendMail", 'LA\TimesheetsController@sendMail');

    /* ================== Sidebar_Menu_Accesses ================== */
    Route::resource(config('laraadmin.adminRoute') . '/sidebar_menu_accesses', 'LA\Sidebar_Menu_AccessesController');
    Route::get(config('laraadmin.adminRoute') . '/sidebar_menu_access_dt_ajax', 'LA\Sidebar_Menu_AccessesController@dtajax');

    /* ================== Leaves_index ================== */
    Route::resource(config('laraadmin.adminRoute') . '/leaves', 'LA\LeaveMasterController');
    Route::post(config('laraadmin.adminRoute') . '/leaves/store', 'LA\LeaveMasterController@store');
    Route::get('/approveLeave', 'LA\LeaveMasterController@ajaxApproveLeave');
    Route::get(config('laraadmin.adminRoute') . '/leave/teamMember', 'LA\LeaveMasterController@teamMemberIndex');
    Route::post(config('laraadmin.adminRoute') . '/leave/withdraw', 'LA\LeaveMasterController@ajaxWithdraw');
    Route::get(config('laraadmin.adminRoute') . "/downloadLeave", 'LA\LeaveMasterController@downloadLeave');
    Route::post("/exportLeaveToAuthority", 'LA\LeaveMasterController@ajaxExportLeaveToAuthority');
    Route::post(config('laraadmin.adminRoute') . '/leave/Datatable', 'LA\LeaveMasterController@ajaxDatatable');
    
    Route::get('/isLeave', 'LA\LeaveMasterController@ajaxIsApprovedLeave');

    /* ================== Sidebar_Menu_Leaves_ViewData ================== */
    Route::resource(config('laraadmin.adminRoute') . '/leaves', 'LA\LeaveMasterController');


    /* ================== Task_Roles ================== */
    Route::resource(config('laraadmin.adminRoute') . '/task_roles', 'LA\Task_RolesController');
    Route::get(config('laraadmin.adminRoute') . '/task_role_dt_ajax', 'LA\Task_RolesController@dtajax');




    /* ================== Leave_Types ================== */
    Route::resource(config('laraadmin.adminRoute') . '/leave_types', 'LA\Leave_TypesController');
    Route::get(config('laraadmin.adminRoute') . '/leave_type_dt_ajax', 'LA\Leave_TypesController@dtajax');



    /* ================== Resource_Allocations ================== */
    Route::resource(config('laraadmin.adminRoute') . '/resource_allocations', 'LA\Resource_AllocationsController');
    Route::post(config('laraadmin.adminRoute') . '/resource_allocation_dt_ajax', 'LA\Resource_AllocationsController@dtajax');

    /* ================== Holidays_Lists ================== */
    Route::resource(config('laraadmin.adminRoute') . '/holidays_lists', 'LA\Holidays_ListsController');
    Route::get(config('laraadmin.adminRoute') . '/holidays_list_dt_ajax', 'LA\Holidays_ListsController@dtajax');

    /* ================== Projects_Sprints ================== */
    Route::resource(config('laraadmin.adminRoute') . '/projects_sprints', 'LA\Projects_SprintsController');
    Route::post(config('laraadmin.adminRoute') . '/sprintList', 'LA\Projects_SprintsController@ajaxSprintList');
    Route::post(config('laraadmin.adminRoute') . '/checkProjectDates', 'LA\Projects_SprintsController@ajaxProjectDatesCheck');

    /* ================== Feedback ================== */
    Route::resource(config('laraadmin.adminRoute') . '/feedback', 'LA\FeedbackController');
    Route::get(config('laraadmin.adminRoute') . '/feedback_dt_ajax', 'LA\FeedbackController@dtajax');

    /* ================== Comp_Off_Managements ================== */
    Route::resource(config('laraadmin.adminRoute') . '/comp_off_managements', 'LA\Comp_Off_ManagementsController');
    Route::get(config('laraadmin.adminRoute') . '/comp_off_management_dt_ajax/{teamMember}', 'LA\Comp_Off_ManagementsController@dtajax');
    Route::get(config('laraadmin.adminRoute') . '/comp_off_management/teamMemberCompOff', 'LA\Comp_Off_ManagementsController@teamMemberList');
    Route::get('/approveCompOff', 'LA\Comp_Off_ManagementsController@ajaxApproveCompOff');

    /* ================== Notifications ================== */
    Route::get('/markReadNotification/{count}', function($count) {
        Notification::markAllAsRead($count);
        return view('la.layouts.partials.notification');
    });
    Route::get('/showAllNotification', function() {
        return view('la.notificationList');
    });
    
    
    /* ================== Reimbursement_Types ================== */
    Route::resource(config('laraadmin.adminRoute') . '/reimbursement_types', 'LA\Reimbursement_TypesController');
    Route::get(config('laraadmin.adminRoute') . '/reimbursement_type_dt_ajax', 'LA\Reimbursement_TypesController@dtajax');

	/* ================== Reimbursement_Forms ================== */
	Route::resource(config('laraadmin.adminRoute') . '/reimbursement_forms', 'LA\Reimbursement_FormsController');
	Route::post(config('laraadmin.adminRoute') . '/reimbursement_form_dt_ajax', 'LA\Reimbursement_FormsController@dtajax');
    Route::get( "admin/reimbursement_form/teamMemberReimbursement", 'LA\Reimbursement_FormsController@teamMemberReimbursement');
    Route::post(config('laraadmin.adminRoute') . '/reimbursement_forms/store', 'LA\Reimbursement_FormsController@store');
    Route::get('/approvereimbursement', 'LA\Reimbursement_FormsController@ajaxApproveReimbursement');
    Route::get(config('laraadmin.adminRoute') . '/reimbursement_images_delete_ajax', 'LA\Reimbursement_FormsController@removeimagesajax');
});
 