<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\ReportModel;;
use Mpdf\Mpdf;
use App\Models\ProjectunitModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use App\Models\TaskStaffActivityModel;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\MastertaskModel;

use App\Models\ProjectsModel;
use App\Models\TaskModel;

class ReportController extends controller
{   
    protected $reportModel;
    protected $taskModel;
    protected $mastertaskModel;
    public function __construct() {
        $this->reportModel = new ReportModel();
        $this->taskModel = new TaskModel();
        $this->mastertaskModel = new MastertaskModel();
    }
    public function index()
    {   
        $page = (!haspermission('','report') ? lang('Custom.accessDenied') : 'Select the task you want to report' );
        $rojectUnitModel = new ProjectunitModel();
        $projectModel = new ProjectsModel();

        $projectUnits = $rojectUnitModel->where('status',1)->findAll();
        $projectsList = $projectModel->where('is_active',1)->findAll();
        return view('admin/reports/tasklist',compact('page','projectUnits','projectsList'));
    }
    public function userReport()
    {   
        $page = (!haspermission('','user_report_view') ? lang('Custom.accessDenied') : 'Select the task you want to report' );
        $rojectUnitModel = new ProjectunitModel();
        $projectModel = new ProjectsModel();

        $projectUnits = $rojectUnitModel->where('status',1)->findAll();
        $projectsList = $projectModel->where('is_active',1)->findAll();
        return view('admin/reports/userReportList',compact('page','projectUnits','projectsList'));
    }

    //only assigned users
    public function taskReportList() {
        if(!haspermission('','user_report_view')) {
           return $this->response->setJSON([
                'success' => false,
                'message' => lang('Custom.accessDenied')
            ]);
        }
        if(!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Custom.invalidRequest')
            ]);
        }
        $search = $this->request->getGet('search');
        $startDate = $this->request->getPost('startDate');
        $endDate = $this->request->getPost('endDate');
        if(empty($startDate)) {
            $startDate = date('Y-m-d', strtotime('-3 days'));
        }
        if(empty($endDate)) {
            $endDate = date('Y-m-d', strtotime('-1 days'));
        }

       $taskModel = new TaskModel();

        $builder = $taskModel
            ->select('tasks.title, tasks.task_gen_date, tasks.id')
            ->join('task_staff_activities as tsa', 'tasks.id = tsa.task_id')
            ->where('tasks.tasktype', 1)
            ->where('tsa.staff_id', session('user_data')['id'])
            ->where('tasks.task_gen_date >=', $startDate)
            ->where('tasks.task_gen_date <=', $endDate);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('tasks.title', $search)
            ->groupEnd();
        }

        $tasks = $builder
            ->groupBy(['DATE(tasks.task_gen_date)', 'tasks.created_from_template'])
            ->orderBy('tasks.task_gen_date', 'DESC')
            ->get()
            ->getResultArray();

        if (!empty($tasks)) {
            foreach ($tasks as &$task) {
                $task['url'] = 'tasklist/' . encryptor($task['id']);
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $tasks
        ]);
    }
    //admin
    public function reportTaskList() {
        // if(!haspermission('','report')) {
        //    return $this->response->setJSON([
        //         'success' => false,
        //         'message' => lang('Custom.accessDenied')
        //     ]);
        // }
        if(!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Custom.invalidRequest')
            ]);
        }
        $search = $this->request->getPost('search');
        $startDate = $this->request->getPost('startDate');
        $endDate = $this->request->getPost('endDate');
        if(empty($startDate)) {
            $startDate = date('Y-m-d', strtotime('-3 days'));
        }
        if(empty($endDate)) {
            //today
            //$endDate = date('Y-m-d', strtotime('-1 days'));
            $endDate = date('Y-m-d');
        }

        $taskModel = new TaskModel();

        // Build query
        $builder = $taskModel
            ->select('tasks.title, tasks.task_gen_date, tasks.id')
            ->where('tasktype', 1)
            ->where('task_gen_date >=', $startDate)
            ->where('task_gen_date <=', $endDate)
            ->groupBy(['DATE(task_gen_date)', 'created_from_template']); // safer
           

        // Search
        if (!empty($search)) {
            $builder->like('tasks.title', $search);
        }

        $tasks = $builder
            ->orderBy('task_gen_date', 'DESC')
            ->get()
            ->getResultArray();

        // Add URL
        if (!empty($tasks)) {
            foreach ($tasks as &$task) {
                $task['url'] = 'tasklist/' . encryptor($task['id']);
            }
        }
        // Response
        return $this->response->setJSON([
            'success' => true,
            'data' => $tasks
        ]);
        
    }

    public function reportList($id)
    {
        $id = decryptor($id);
        $taskModel = new TaskModel();
        $task = $taskModel->where('id', $id)->get()->getRow();
        $page = ($task) ? $task->title.' '.date('d-m-Y',strtotime($task->task_gen_date)) : 'No Task Found';
        $rojectUnitModel = new ProjectunitModel();
        $projectModel = new ProjectsModel();

        $projectUnits = $rojectUnitModel->where('status',1)->findAll();
        $projectsList = $projectModel->where('is_active',1)->findAll();
        
        return view('admin/reports/index',compact('id','page','projectUnits','projectsList'));
    }
    public function myReportList($id)
    {
        $id = decryptor($id); 
        $taskModel = new TaskModel();
        $task = $taskModel->where('id', $id)->get()->getRow();
        $page = ($task) ? $task->title.' '.date('d-m-Y',strtotime($task->task_gen_date)) : 'No Task Found';
        $rojectUnitModel = new ProjectunitModel();
        $projectModel = new ProjectsModel();

        $projectUnits = $rojectUnitModel->where('status',1)->findAll();
        $projectsList = $projectModel->where('is_active',1)->findAll();
        
        return view('admin/reports/userReport',compact('id','page','projectUnits','projectsList'));
    }
    // function list()
    // {   
    //     $reportModel = new ReportModel();
    //     $search = $this->request->getGet('search');
    //     $filer = $this->request->getGet('filer');
    //     $startDate = $this->request->getGet('startDate');
    //     $endDate = $this->request->getGet('endDate');
    //     $prounit = $this->request->getGet('prounit');
  
    //    $reportResult = $reportModel->getReports($search, $filer, $startDate, $endDate, $prounit);

    //     $resultData = [];
    //     $activityHeaders = [];

    //     /**
    //      * STEP 1: GROUP BY TASK
    //      */
    //     foreach ($reportResult as $repo) {

    //         $taskId = $repo['taskId'];

    //         if (!isset($resultData[$taskId])) {
    //             $resultData[$taskId] = [
    //                 'taskId'        => $taskId,
    //                 'tasktitle'     => $repo['task_title'],
    //                 'storeName'     => $repo['store_name'],
    //                 'oldStoreName'  => $repo['oldstore_name'],
    //                 'oracleCode'    => $repo['oracle_code'],
    //                 'activities'    => []
    //             ];
    //         }

    //         $activityIndex = count($resultData[$taskId]['activities']);

    //         // store header once
    //         if (!isset($activityHeaders[$activityIndex])) {
    //             $activityHeaders[$activityIndex] =
    //                 $repo['activity_title'] . ' (ID: ' . $repo['activity_id'] . ')';
    //         }

    //         $resultData[$taskId]['activities'][] = [
    //             'activity_id'    => $repo['activity_id'],
    //             'activity_title' => $repo['activity_title'],
    //             'status'         => $repo['activity_status'],
    //             'comment'        => !empty($repo['last_comment'])
    //                                 ? $repo['last_comment']
    //                                 : 'Not commented',
    //             'comment_time'   => $repo['comment_time']
    //         ];
    //     }

    //     /**
    //      * STEP 2: MAX ACTIVITIES (FOR COLUMNS)
    //      */
    //     $maxActivities = 0;
    //     foreach ($resultData as $task) {
    //         $maxActivities = max($maxActivities, count($task['activities']));
    //     }

    //     /**
    //      * STEP 3: PREPARE EXCEL ROWS
    //      */
    //     $excelRows = [];
    //     $sl = 1;

    //     foreach ($resultData as $task) {

    //         $row = [
    //             'sl_no'      => $sl++,
    //             'code'       => $task['oracleCode'],
    //             'store_name' => $task['storeName'],
    //             'old_name'   => $task['oldStoreName'],
    //         ];

    //         foreach ($task['activities'] as $i => $activity) {
    //             $row['activity_' . ($i + 1)] = $activity['comment'];
    //         }

    //         // fill empty columns
    //         for ($i = count($task['activities']) + 1; $i <= $maxActivities; $i++) {
    //             $row['activity_' . $i] = '';
    //         }

    //         $excelRows[] = $row;
    //     }

    //     return view('admin/reports/report', [
    //         'excelRows'       => $excelRows,
    //         'maxActivities'   => $maxActivities,
    //         'activityHeaders' => $activityHeaders
    //     ]);

    //     // Optional: reset array keys
    //     $resultData = array_values($resultData);
    //     echo '<pre>';
    //     print_r($resultData);

        
    //     //return $this->response->setJSON(['success' => true , 'result' => $reportResult ]);
       
    // }
    public function userReportList()
    {
        $data = $this->request->getGet();
        $data['user'] = 1;
        $repo =  $this->reportUi($data,true);
    
       return $this->response->setJSON($repo);
    }

    public function list() {
       $repo =  $this->reportUi($this->request->getGet(),false);
       return $this->response->setJSON($repo);
        
    }


   public function reportUi($get,$reportType)
    {
        $reportModel = new ReportModel();

        $search    = $this->request->getGet('search');
        $filter    = $this->request->getGet('filter');
        $startDate = $this->request->getGet('startDate');
        $endDate   = $this->request->getGet('endDate');
        $prounit   = $this->request->getGet('prounit');
        $project   = $this->request->getGet('project');
        $range     = $this->request->getGet('range');
        $taskId    = decryptor($this->request->getGet('taskId')); // for task 

        $today = date('Y-m-d');
      

        $getTask = $reportModel->where('id',$taskId)->get()->getRow();
          if(empty($startDate)){
            $startDate = date('Y-m-d', strtotime($getTask->task_gen_date));
            $endDate   = date('Y-m-d', strtotime($getTask->task_gen_date));
        }

        $userId = $get['user'] ?? false; 


        $reportResult = $reportModel->getReports(
            $search,
            $filter,
            $startDate,
            $endDate,
            $prounit,
            $project,
            '','','',
            $getTask->created_from_template,
            $getTask->task_gen_date,
            $userId
        );
        
        // $reportResult = $reportModel->getReports($search, $filter, $startDate, $endDate, $prounit, $project, false,);
        //echo $reportModel->getLastQuery();

        $groupedTasks = [];
        $activityHeaders = [];

        /* ============================================
        STEP 1: GROUP DATA CORRECTLY
        ============================================ */

        foreach ($reportResult as $repo) {

            $taskId     = $repo['taskId'];
            $activityId = $repo['activity_id'];
            $tsaactivityId = $repo['tsaactivityId'];

            if (!isset($groupedTasks[$taskId])) {
                $groupedTasks[$taskId] = [
                    'oracleCode'   => $repo['oracle_code'],
                    'polarisCode'   => $repo['polaris_code'],
                    'storeName'    => $repo['store_name'],
                    'oldStoreName' => $repo['oldstore_name'],
                    'task'         => $repo['task_title'],
                    'date'         => date('d-m-Y', strtotime($repo['task_gen_date'])),
                    'assignAllocatedTo' => $repo['allocated_to'],
                    'assignAssignedTo'  => $repo['assigned_to'],
                    'activities'   => []
                ];
            }

            // Store unique activity headers
            if (!empty($activityId) && !isset($activityHeaders[$activityId])) {
                $activityHeaders[$activityId] = $repo['activity_title'];
            }

            // Map activity properly using activityId as KEY
            // if (!empty($activityId)) {
            //     $groupedTasks[$taskId]['activities'][$activityId] = [
            //         //the comment zero is not showing show the comment 0
            //         'comment'    => (!empty($repo['last_comment']) && $repo['last_comment'] != 'Nill')
            //                             ? $repo['last_comment']
            //                             : 'Nill',
            //         'activityId' => encryptor($tsaactivityId) ?? '',
            //         'taskId'     => encryptor($taskId) ?? ''
            //     ];
            // }

            if (!empty($activityId)) {
                $groupedTasks[$taskId]['activities'][$activityId] = [
                    'comment' => (isset($repo['last_comment']) && $repo['last_comment'] !== 'Nill')
                                    ? $repo['last_comment']
                                    : 'Nill',
                    'activityId' => encryptor($tsaactivityId) ?? '',
                    'taskId'     => encryptor($taskId) ?? ''
                ];
            }

        }

        /* ============================================
        STEP 2: SORT ACTIVITIES
        ============================================ */

        ksort($activityHeaders);

        /* ============================================
        STEP 3: BUILD TABLE HEADERS
        ============================================ */

        $headers = [
            'SL NO',
            'ORACLE CODE',
            'POLARIS CODE',
            'STORE NAME',
            'OLD NAME',
            'DATE',
            'TASK',
            'ALLOCATED TO',
            'ASSIGNED TO'
        ];

        foreach ($activityHeaders as $activityId => $title) {
            $headers[] = $title . ' (' . $activityId . ')';
        }

        /* ============================================
        STEP 4: BUILD TABLE ROWS
        ============================================ */

        $rows = [];
        $sl = 1;

        foreach ($groupedTasks as $task) {

            $row = [
                $sl++,
                $task['oracleCode'],
                $task['polarisCode'],
                $task['storeName'],
                $task['oldStoreName'],
                $task['date'],
                $task['task'],
                $task['assignAllocatedTo'],
                $task['assignAssignedTo'],
            ];

            foreach ($activityHeaders as $activityId => $title) {

                if (isset($task['activities'][$activityId])) {

                    // Activity assigned
                    $row[] = $task['activities'][$activityId];

                } else {

                    // Activity NOT assigned → show 
                    $row[] = [
                        'comment' => '---',
                        'comment_id' => ''
                    ];
                }
            }

            $rows[] = $row;
        }

        /* ============================================
        FINAL JSON RESPONSE
        ============================================ */

        // return $this->response->setJSON([
        //     'success' => true,
        //     'headers' => $headers,
        //     'result'  => $rows
        // ]);
        return [
            'success' => true,
            'headers' => $headers,
            'result'  => $rows
        ];
    }

    public function generateReport()
    {
        if (ob_get_length()) ob_end_clean();

        $reportModel = new ReportModel();
        $taskStaffActivityModel = new TaskStaffActivityModel();

        $search    = $this->request->getGet('search');
        $filter    = $this->request->getGet('filter');
        $startDate = $this->request->getGet('startDate');
        $endDate   = $this->request->getGet('endDate');
        $prounit   = $this->request->getGet('projectUnit');
        $project   = $this->request->getGet('project');
        $taskId    = decryptor($this->request->getGet('taskId'));

        $getTask = $reportModel->where('id',$taskId)->get()->getRow();
        if(empty($startDate)){
            $startDate = date('Y-m-d', strtotime($getTask->task_gen_date));
            $endDate   = date('Y-m-d', strtotime($getTask->task_gen_date));
        }

        $userId = $get['user'] ?? false; 


        $reportResult = $reportModel->getReports(
            $search,
            $filter,
            $startDate,
            $endDate,
            $prounit,
            $project,
            '','','',
            $getTask->created_from_template,
            $getTask->task_gen_date,
            $userId
        );

        //$reportResult = $reportModel->getReports($search, $filter, $startDate, $endDate, $prounit, $project, $taskId);

        $groupedTasks = [];
        $activityHeaders = []; // activity_id => title

        /* ============================================
        SAME LOGIC AS list() METHOD
        ============================================ */

        foreach ($reportResult as $repo) {

            $taskId     = $repo['taskId'];
            $activityId = $repo['activity_id'];

            if (!isset($groupedTasks[$taskId])) {
                $groupedTasks[$taskId] = [
                    'taskId'       => $taskId,
                    'oracleCode'   => $repo['oracle_code'],
                    'polarisCode'   => $repo['polaris_code'],
                    'storeName'    => $repo['store_name'],
                    'taskgendate'  => $repo['task_gen_date'],
                    'oldStoreName' => $repo['oldstore_name'],
                    'date'         =>  date('Y-m-d', strtotime($repo['task_gen_date'])),
                    'task'         => $repo['task_title'],
                    'allocated_to' => $repo['allocated_to'],
                    'assigned_to'  => ($repo['allocated_to_id'] == $repo['assigned_to_id'] ? NULL : $repo['assigned_to']),
                    'activities'   => []
                ];
            }

            if (!empty($activityId) && !isset($activityHeaders[$activityId])) {
                $activityHeaders[$activityId] = $repo['activity_title'];
            }

            if (!empty($activityId)) {
                $groupedTasks[$taskId]['activities'][$activityId] = [
                    'comment'        => $repo['last_comment'] ?: 'Not commented',
                    'taskStatus'     => $repo['taskStatus'],
                    'activityStatus' => $repo['activityStatus']
                ];
            }
        }

        ksort($activityHeaders);

        /* ============================================
        CREATE EXCEL
        ============================================ */

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $header = ['SL NO', 'ORACLE CODE', 'POLARIS CODE', 'STORE NAME', 'OLD NAME', 'DATE','TASK','ALLOCATED TO', 'ASSIGNED TO'];

        foreach ($activityHeaders as $activityId => $title) {
            $header[] = $title;
        }

        $sheet->fromArray($header, null, 'A1');

        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle("A1:{$highestColumn}1")->getFont()->setBold(true);
        $sheet->getStyle("A1:{$highestColumn}1")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $rowNo = 2;
        $sl = 1;

        foreach ($groupedTasks as $task) {

            $row = [
                $sl++,
                $task['oracleCode'],
                $task['polarisCode'],
                $task['storeName'],
                $task['oldStoreName'],
                $task['date'],
                $task['task'],
                $task['allocated_to'],
                $task['assigned_to']
            ];

            foreach ($activityHeaders as $activityId => $title) {

                if (isset($task['activities'][$activityId])) {

                    $act = $task['activities'][$activityId];

                    // Update comment status if completed
                    if ($act['taskStatus'] == 'Completed' && $act['activityStatus'] == 'completed' ) {
                           if($task['taskgendate'] < date('Y-m-d',strtotime('-1 day')))
                            {
                                //   $taskStaffActivityModel->where([
                                //     'task_activity_id' => $activityId,
                                //     'task_id'          => $task['taskId']
                                // ])->set(['commet_status' => 2])->update();
                            }
                    }

                    $row[] = $act['comment'];

                } else {
                    $row[] = '';
                }
            }

            $sheet->fromArray($row, null, 'A'.$rowNo++);
        }

        /* ============================================
        AUTO SIZE
        ============================================ */

        $highestColumnIndex = Coordinate::columnIndexFromString($sheet->getHighestColumn());

        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $columnLetter = Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
            $sheet->getStyle($columnLetter)->getAlignment()->setWrapText(true);
        }

        /* ============================================
        DOWNLOAD
        ============================================ */

        $filename = 'task_report_'.date('Y-m-d_H-i-s').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function historyReport() {
        $page = (!haspermission('','report') ? lang('Custom.accessDenied') : 'Select the task you want to report' );
        $rutes = (haspermission('','report') ? 'admin/reports/history-report' : '404page' );    
        $rojectUnitModel = new ProjectunitModel();
        $projectModel = new ProjectsModel();

        $projectUnits = $rojectUnitModel->where('status',1)->findAll();
        $projectsList = $projectModel->where('is_active',1)->findAll();
        //create from template grouping not working
        //$tasksByprojectUnits = $this->taskModel->where(['ui' =>1,'tasktype' => 1])->groupBy('project_unit,created_from_template')->get()->getResult(); 
        $tasksByprojectUnits = $this->mastertaskModel->where('status','active')->get()->getResult();
        //echo $this->taskModel->getLastQuery();
        
        
        return view($rutes,compact('projectUnits','projectsList','page','tasksByprojectUnits'));
    }
    public function historyReportList($id=false) {
        if(!haspermission('','report')){
            return lang('Custom.accessDenied');
        }
        $rutes = (haspermission('','report') ? 'admin/reports/history-report-tasklist' : '404page' );
        $template = $this->request->getGet('task');
        //date date=2026-03-22+to+2026-03-28 splt to satrt date and end date 
        $date = $this->request->getGet('date');
        $startDate = date('Y-m-d',strtotime('-1 day'));
        $endDate = date('Y-m-d',strtotime('-1 day'));
        if($date){
            $date = explode('to', $date);
            $startDate = $date[0];
            $endDate = $date[1];
        }
        $id = decryptor($id);
        //projectunit
        $projectunit = $this->request->getGet('projectunit');
        $taskModel = new TaskModel();
        //select taskks from task table where created_from_template = $template and task_gen_date = $date
        $builder = $taskModel->where('created_from_template', $template)->where('task_gen_date >=', $startDate)->where('task_gen_date <=', $endDate);
        if($projectunit != 'all'){
            $builder->where('project_unit', $projectunit);
        }
        $task = $builder->get()->getResult();
        //i have get 11 tasks i want to list each activities send ids to report model ,121,55,22,81,58,66,67,68,69,70,71, how to send this ids to report model
        $taskId = [];
        if(!empty($task)){
            foreach($task as $key => $value){
                $taskId[] = $value->id;
            }
            $taskId = implode(',', $taskId);
        }
        if(!empty($taskId)){
            $historyReport = $this->reportModel->generateHistoryReport($taskId);
        }else{
            $historyReport = [];
        }
           
        $page = '';//($task) ? $task->title.' '.date('d-m-Y',strtotime($task->task_gen_date)) : 'No Task Found';
        $rojectUnitModel = new ProjectunitModel();
        $projectModel = new ProjectsModel();

        $projectUnits = $rojectUnitModel->where('status',1)->findAll();
        $projectsList = $projectModel->where('is_active',1)->findAll();
        $result = [];

        foreach ($historyReport as $row) {

            $taskId = $row['id'];
            $activityId = $row['activity_id']; // ✅ FIXED

            // TASK LEVEL
            if (!isset($result[$taskId])) {
                $result[$taskId] = [
                    'task_id' => $taskId,
                    'task_title' => $row['title'],
                    'projectUnit' => $row['store'],
                    'task_date' => date('M d, Y', strtotime($row['task_gen_date'])),
                    'activities' => []
                ];
            }

            // ACTIVITY LEVEL (ONLY ONCE)
            if (!isset($result[$taskId]['activities'][$activityId])) {
                $result[$taskId]['activities'][$activityId] = [
                    'activity_id' => $activityId,
                    'activity_title' => $row['activity_title'],
                    'activity_status' => $row['activityStatus'],
                    'activity_description' => $row['activity_description'],
                    'comments' => []
                ];
            }

            // COMMENTS LEVEL (ONLY REAL COMMENTS)
            if ($row['comment'] !== null && $row['comment'] !== '') {

                // prevent duplicate same comment
                $exists = false;

                foreach ($result[$taskId]['activities'][$activityId]['comments'] as $c) {
                    if (
                        $c['comment'] == $row['comment'] &&
                        $c['user_name'] == $row['user_name'] &&
                        $c['comment_date'] == $row['comment_date']
                    ) {
                        $exists = true;
                        break;
                    }
                }

                if (!$exists) {
                    $result[$taskId]['activities'][$activityId]['comments'][] = [
                        'comment' => $row['comment'], // ✅ includes "0"
                        'user_name' => $row['user_name'],
                        'comment_date' => $row['comment_date']
                    ];
                }
            }
        }

        // Optional: reset indexes
        $result = array_values($result);

        foreach ($result as &$task) {
            $task['activities'] = array_values($task['activities']);
        }
        // echo "<pre>";
        // print_r($result);
        // echo "</pre>";
        // exit;
        $requestUrl =  $this->request->getGet();
        //$tasksByprojectUnits = $this->taskModel->where(['ui' =>1,'tasktype' => 1])->groupBy('project_unit')->get()->getResult(); 
        $tasksByprojectUnits = $this->mastertaskModel->where('status','active')->get()->getResult();
        return view($rutes,compact('id','page','projectUnits','projectsList','result','requestUrl','tasksByprojectUnits'));
    }

    public function historycommentsReportList() {
        if(!haspermission('','report')){
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Custom.accessDenied')
            ]);
        }
        if(!$this->request->isAJAX()){
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Custom.invalidRequest')
            ]);
        }

        $date = $this->request->getGet('date');
        $startDate = date('Y-m-d',strtotime('-1 day'));
        $endDate = date('Y-m-d',strtotime('-1 day'));
        if($date){
            $date = explode('to', $date);
            $startDate = $date[0];
            $endDate = $date[1];
        }

         $builder = $taskModel->where('created_from_template', $template)->where('task_gen_date >=', $startDate)->where('task_gen_date <=', $endDate);
        if($projectunit != 'all'){
            $builder->where('project_unit', $projectunit);
        }
        $task = $builder->get()->getResult();

        
        $taskId = [];
        if(!empty($task)){
            foreach($task as $key => $value){
                $taskId[] = $value->id;
            }
            $taskId = implode(',', $taskId);
        }
        if(!empty($taskId)){
            $historyReport = $this->reportModel->generateHistoryReport($taskId);
        }else{
            $historyReport = [];
        }
       
        $result = [];
        if(!empty($historyReport)){
           foreach($historyReport as $key => $value){
            if(!isset($result[$value['activity_id']])){
                $result[$value['activity_id']] = [
                    'taskTitle' => $value['title'],
                    'taskDate' => date('M d, Y',strtotime($value['task_gen_date'])),
                    'projectUnit' => $value['store'],
                    'activity_id' => $value['activity_id'],
                    'activity_title' => $value['activity_title'],
                    'activity_status' => $value['activityStatus'],
                    'activity_description' => $value['activity_description'],
                    'activity_comments' => []
                ];
            }
            $result[$value['activity_id']]['activity_comments'][] = [
                'comment' => $value['comment'],
                'comment_by' => $value['user_name'],
                'comment_date' => date('M-d-Y H:i:s',strtotime($value['comment_date']))
            ];
           }
        }
        $result = array_values($result);
       return $this->response->setJSON([
        'status' => true,
        'result' => $result
       ]);

    }

    function historyReportDownload($id = false){
       
        $taskModel = new TaskModel();
         $template = $this->request->getGet('task');
        //date date=2026-03-22+to+2026-03-28 splt to satrt date and end date 
        $date = $this->request->getGet('date');
        $date = explode('to', $date);
        $startDate = $date[0];
        $endDate = $date[1];
        $id = decryptor($id);
        //projectunit
        $projectunit = $this->request->getGet('projectunit');
        
        $builder = $taskModel->where('created_from_template', $template)->where('task_gen_date >=', $startDate)->where('task_gen_date <=', $endDate);
        if($projectunit != 'all'){
            $builder->where('project_unit', $projectunit);
        }
        $task = $builder->get()->getResult();

        if (!$task) {
            return "No Task Found";
        }

        $taskId = [];
        if(!empty($task)){
            foreach($task as $key => $value){
                $taskId[] = $value->id;
            }
            $taskId = implode(',', $taskId);
        }
        $historyReport = $this->reportModel->generateHistoryReport($taskId);

        // 🔹 Format result (group by activity)
        $result = [];

        

        // 🔹 Get report data

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $row = 1;

        // 🔹 TITLE
        $findtask = $taskModel->where('id', $taskId)->first();
        $sheet->setCellValue('A' . $row, 'History Report : '.$findtask['title'].'('. date('d-m-Y',strtotime($startDate)).' to '. date('d-m-Y',strtotime($endDate)).')');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $row += 2;

        // 🔹 HEADER
        $headers = ['SL NO','DATE','STORE NAME','OLD NAME','ORACLE CODE','POLARIS CODE','TASK','ACTIVITY','COMMENTS','COMMENTED BY','COMMENTED DATE'];

        $col = 'A';
        foreach ($headers as $head) {
            $sheet->setCellValue($col . $row, $head);
            $col++;
        }

        // 🔹 HEADER STYLE (GREEN)
        $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E7D32'] // green
            ]
        ]);

        $row++;

        // 🔹 DATA
        $sl = 1;

       $unique = [];

        foreach ($historyReport as $data) {

            // ✅ skip empty comments (but allow "0")
            if ($data['comment'] === null || $data['comment'] === '') {
                continue;
            }

            // ✅ create unique key (prevents duplicates)
            $key = md5(
                $data['id'] .
                $data['activity_id'] .
                $data['comment'] .
                $data['user_name'] .
                $data['comment_date']
            );

            if (isset($unique[$key])) {
                continue; // skip duplicate
            }

            $unique[$key] = true;

            // ✅ WRITE TO EXCEL
            $sheet->setCellValue('A' . $row, $sl++);
            $sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($data['task_gen_date'])));
            $sheet->setCellValue('C' . $row, $data['store']);
            $sheet->setCellValue('D' . $row, $data['oldstore_name']);
            $sheet->setCellValue('E' . $row, $data['oracle_code']);
            $sheet->setCellValue('F' . $row, $data['polaris_code']);
            $sheet->setCellValue('G' . $row, $data['title']);
            $sheet->setCellValue('H' . $row, $data['activity_title']);
            $sheet->setCellValue('I' . $row, $data['comment']); // ✅ "0" works
            $sheet->setCellValue('J' . $row, $data['user_name']);
            $sheet->setCellValue('K' . $row, date('d-m-Y H:i', strtotime($data['comment_date'])));

            $row++;
        }
        // 🔹 BORDER STYLE
        $sheet->getStyle('A3:K' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);

        // 🔹 AUTO WIDTH
        foreach (range('A','K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 🔹 DOWNLOAD
        $filename = 'task_history_report_'.date('Y-m-d_H-i-s').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}