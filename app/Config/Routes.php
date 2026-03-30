<?php
namespace Config;

use App\Controllers\Appointments;

$routes = Services::routes();

if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

$routes->get('/','Auth::login');
$routes->get('login','Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('logout', 'LogoutController::index');

// $routes->group('', ['filter' => 'noauth'], function($routes) {
//     $routes->get('login', 'Auth::login');
//     $routes->post('login', 'Auth::attemptLogin');
// });

$routes->group('', ['filter' => 'auth'], function($routes) 
{
    $routes->get('dashboard', 'Home::index');
    $routes->get('settings', 'Settings::index');
    $routes->get('notifications/count','Notification::notifications');
    $routes->post('settings/save','Settings::save');
    $routes->get('notifications/fetch','Notification::load');
    $routes->get('notifications','Notification::myNotifications'); 
    $routes->post('notification-list','Notification::allnotification');
    $routes->post('tasks/notification-list','Notification::allnotification');
    $routes->post('notification/view','Notification::view');
    $routes->get('admin/task/pending','TaskController::index/pending');
    $routes->get('admin/task/in-progress','TaskController::index/in-progress');
    //permissions 
    $routes->get('permisions','Permissions::checkpermission');
    $routes->get('permisions/list','Permissions::list');
    $routes->get('permissions/check-permission/(:any)','Permissions::checkpermission/$1');
    $routes->post('permissions/save','Permissions::save');
    $routes->get('permissions/controls','Permissions::controls');

    //staff
    $routes->get('staff','Staff::index');
    $routes->get('staff/create','Staff::create');
    $routes->post('staff/save','Staff::save');
    $routes->Post('staff/list','Staff::list');
    $routes->get('staff/edit/(:any)','Staff::create/$1');
    $routes->post('staff/delete','Staff::delete');
    $routes->post('branch-staff','Staff::branchStaff');
    $routes->get('staff-upload', 'Staff::bulkindex');
    $routes->post('staff-upload/uploadExcel', 'Staff::uploadExcel');
    //choppies-team 
    $routes->get('choppies-team','ChoppiesTeamController::index');
    $routes->post('choppies-team/save','ChoppiesTeamController::save');
    $routes->post('choppies-team/list','ChoppiesTeamController::list');
    $routes->get('choppies-team/edit/(:any)','ChoppiesTeamController::create/$1');
    $routes->get('choppies-team/create','ChoppiesTeamController::create');
    $routes->post('choppies-team/delete','ChoppiesTeamController::delete');
    //branch
    $routes->get('branches','Branches::index');
    $routes->get('branch/create','Branches::create');
    $routes->post('branch/save','Branches::save');
    $routes->get('branch/search','Branches::search');
    $routes->get('branches/edit/(:any)','Branches::create/$1');
    $routes->post('branch/delete','Branches::delete');
    $routes->get('branches/view/(:any)','Branches::view/$1');
    //services
    $routes->get('services-list','Services::index');
    $routes->get('services/create','Services::create');
    $routes->post('services/save','Services::save');
    $routes->get('services/list','Services::list');
    $routes->get('service/edit/(:any)','Services::create/$1');
    $routes->post('service/delete','service::delete');
    //Categories
    $routes->get('categories','Category::index');
    $routes->post('category/save','Category::save');
    $routes->get('category/list','Category::categoryList');
    $routes->get('category/edit/(:any)','Category::create/$1');
    $routes->post('category/delete','Category::delete');
    $routes->post('category/unlock','Category::unlock');
    //clients
    $routes->get('clients','Clients::index');
    $routes->get('clients/list','Clients::list');
    $routes->get('clients/create','Clients::create');
    $routes->post('client/save','Clients::save');
    $routes->get('clients/edit/(:any)','Clients::create/$1');
    $routes->post('clients/suggestPhone','Clients::suggestPhone');
    $routes->delete('client/delete/(:any)', 'Clients::delete/$1');
    //Apppintments
    $routes->get('appointments','Appointments::index');
    $routes->get('appointments/booking','Appointments::booking');
    $routes->post('booking/save','Appointments::save');
    $routes->get('appointments/load','Appointments::load');
    //$routes->get('appointments/grid','Appointments::grid');
    //project Unit
    $routes->get('project-unit','ProjectUnitController::index');
    $routes->post('project-unit/save','ProjectUnitController::save');
    $routes->post('project-unit/list','ProjectUnitController::list');
    $routes->post('projectunit/bulk-upload','ProjectUnitController::bulkUpload');
    //tasks 
    $routes->get('tasks','TaskController::index');
    $routes->get('task/create','TaskController::create');
    $routes->post('task/save','TaskController::save');
    $routes->post('task/auto-assign-tasks','TaskController::autoAssignTasksWithStaff');
    $routes->get('task/tasklist','TaskController::list');
    $routes->get('task/view/(:any)','TaskController::view/$1');
    $routes->post('task/update_status','TaskController::update_status');
    $routes->post('task/update','TaskController::save');
    $routes->get('tasks/notification-task/(:any)','TaskController::notificationTask/$1');
    $routes->delete('task/delete/(:any)', 'TaskController::delete/$1');
    $routes->post('task/start','TaskController::start');
    $routes->post('master-task/get-activities','TaskController::getActivities');
    $routes->post('master-task/get-projectunits','TaskController::getProjectunits');
    //master tasks 
    $routes->get('master-task','MasterTaskController::index');
    $routes->post('master-tasks/list','MasterTaskController::list');
    $routes->get('task/create-master-task','MasterTaskController::create');
    $routes->get('master-task/edit/(:any)','MasterTaskController::create/$1');
    $routes->post('master-task/save','MasterTaskController::save');
    $routes->post('master-task/delete/(:any)','MasterTaskController::delete/$1');
    //replay
    $routes->get('tasks/my-tasks','TaskController::myTask');
    $routes->get('task/my-task','TaskController::myTaskList');
    $routes->post('task/replay','ReplayController::save');  
    $routes->post('task-replays','ReplayController::replayHistory');  
    $routes->get('task/mytask/activities/(:any)','ActivitiesController::mYactivities/$1');
    $routes->post('task/activity/replay','ReplayController::activityReplaySave');
    $routes->post('activity-task-replays','ReplayController::activityReplayHistory');
    //dashboard tasks 
    $routes->get('dashboard/tasks','TaskController::list');
    //activity comments
    $routes->post('activity/save-comment','ActivitycommentsController::saveCommets');
    //activities
    $routes->get('activities/(:any)','ActivitiesController::activities/$1');
    $routes->post('activities/save','ActivitiesController::save');
    $routes->get('task/activities','ActivitiesController::activitiList');
    $routes->post('task/activityupdate','ActivitiesController::update');
    $routes->get('task-activity-task','ActivitiesController::allActivityList');
    $routes->post('task/all-activities','ActivitiesController::getAllActivityList');
    $routes->post('activitties/getstaffbytask','ActivitiesController::getStaffBytask');
    $routes->post('activity/lock','ActivitiesController::lock');
    $routes->delete('activity/delete/(:any)','ActivitiesController::delete/$1');
    $routes->delete('activity/multiple-delete','ActivitiesController::multipleDelete');
    //projects 
    $routes->get('projects','ProjectsController::index');
    $routes->get('project/list','ProjectsController::projectList');
    $routes->post('project/save','ProjectsController::save');
    $routes->get('settings/project/edit/(:any)','ProjectsController::create/$1');
    $routes->post('projects/delete','ProjectsController::delete');
    $routes->post('project/unlock','ProjectsController::unlock');
     //report
    $routes->get('reports','ReportController::index');
    $routes->get('reports/list','ReportController::list');
    $routes->post('reports/tasklist','ReportController::reportTaskList');
    $routes->get('report/generate','ReportController::generateReport');
    $routes->get('reports/tasklist/(:any)','ReportController::reportList/$1');
    //history-report
    $routes->get('history-report','ReportController::historyReport');
    $routes->post('history-report/list','ReportController::historycommentsReportList');
    $routes->get('history-report/download','ReportController::historyReportDownload');
    $routes->get('history-report-tasklist/(:any)','ReportController::historyReportList/$1');
    $routes->get('history-report/history-report-tasklist','ReportController::historyReportList');
    //user report
    $routes->get('user-report','ReportController::userReport');
    $routes->get('user-report/userReportList','ReportController::userReportList');
    $routes->get('user-report/generate','ReportController::userReportGenerate');
    $routes->post('user-reports/task-report-list','ReportController::taskReportList');
    $routes->get('my-report/tasklist/(:any)','ReportController::myReportList/$1');
    //role by staff
    $routes->get('role-master','RolemasterController::index');
    $routes->post('rolemaster/save','RolemasterController::save');
    $routes->get('rolemaster/list','RolemasterController::masterList');
    $routes->get('edit-rolemaster/(:any)','RolemasterController::getRoleinfo/$1');
    $routes->post('rolemaster/delete','RolemasterController::delete');
    //group_activity_task_complete 
    $routes->get('task/group_activity_task_complete','TaskController::groupActivityTaskComplete');
    $routes->post('comment-group-activities/save','TaskController::saveCommentGroupActivities');
    $routes->get('user-ui','UseruiController::index');
    $routes->get('user-ui/list','UseruiController::list');
    $routes->post('user-ui/unlock','UseruiController::lock');
    $routes->delete('user-ui/multiple-delete','UseruiController::multipleDelete');

});
$routes->get('autoCron', 'CronController::clonejob');
$routes->cli('CronController/clonejob', 'CronController::clonejob');
$routes->get('close-activity','CronController::closeCompletedtask');
$routes->cli('closeCompletedtask', 'CronController::closeCompletedtask');

$routes->group('api',['filter' => 'auth'], function ($routes) {
    $routes->get('clients/(:num)/projects', 'Clients::clientBystaff/$1');
    $routes->get('clients/(:num)/tasks', 'TaskController::allocatedstaffs/$1');
    $routes->get('project-units/(:any)','ProjectUnitController::getdataFromId/$1');
    $routes->get('get-activity/(:any)','ActivitiesController::getActivity/$1');
});


$routes->get('qry', 'Home::qry');
//$routes->set404Override('App\Controllers\Errors::show404');

// use CodeIgniter\Router\RouteCollection;
// /**
//  * @var RouteCollection $routes
//  */
// $routes->get('/', 'Home::index');
