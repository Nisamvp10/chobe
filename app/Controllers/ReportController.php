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


class ReportController extends controller
{
    public function index()
    {   
        $page = (!haspermission('','report') ? lang('Custom.accessDenied') : 'Reports' );
        $rojectUnitModel = new ProjectunitModel();

        $projectUnits = $rojectUnitModel->where('status',1)->findAll();
        return view('admin/reports/index',compact('page','projectUnits'));
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


    public function list()
    {
        $reportModel = new ReportModel();

        $search    = $this->request->getGet('search');
        $filter    = $this->request->getGet('filter');
        $startDate = $this->request->getGet('startDate');
        $endDate   = $this->request->getGet('endDate');
        $prounit   = $this->request->getGet('prounit');

        $reportResult = $reportModel->getReports(
            $search,
            $filter,
            $startDate,
            $endDate,
            $prounit
        );

        $groupedTasks = [];
        $activityHeaders = []; // activity_id => activity_title

        /* ============================================
        STEP 1: GROUP DATA BY TASK + ACTIVITY_ID
        ============================================ */

        foreach ($reportResult as $repo) {

            $taskId     = $repo['taskId'];
            $activityId = $repo['activity_id'];

            if (!isset($groupedTasks[$taskId])) {
                $groupedTasks[$taskId] = [
                    'oracleCode'   => $repo['oracle_code'],
                    'storeName'    => $repo['store_name'],
                    'oldStoreName' => $repo['oldstore_name'],
                    'task'         => $repo['task_title'],
                    'date'         => date('Y-m-d', strtotime($repo['created_at'])),
                    'assignAllocatedTo' => $repo['allocated_to'],
                    'assignAssignedTo'  => $repo['assigned_to'],
                    'activities'   => []
                ];
            }

            // Save unique activity headers
            if (!empty($activityId) && !isset($activityHeaders[$activityId])) {
                $activityHeaders[$activityId] = $repo['activity_title'];
            }

            // Save comment under correct activity_id
            if (!empty($activityId)) {
                $groupedTasks[$taskId]['activities'][$activityId] =
                    (!empty($repo['last_comment']) && $repo['last_comment'] != 'Nill')
                        ? $repo['last_comment']
                        : 'Nill';
            }
        }
        

        /* ============================================
        STEP 2: SORT ACTIVITIES BY ID (Optional)
        ============================================ */
        ksort($activityHeaders);

        /* ============================================
        STEP 3: BUILD HEADERS
        ============================================ */

        $headers = [
            'SL NO',
            'CODE',
            'STORE NAME',
            'OLD NAME',
            'DATE',
            'TASK',
            'ALLOCATED TO',
            'ASSIGNED TO'
        ];

        foreach ($activityHeaders as $activityId => $title) {
            $headers[] = $title ;//. ' (ID:' . $activityId . ')';
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
                $task['storeName'],
                $task['oldStoreName'],
                $task['date'],
                $task['task'],
                $task['assignAllocatedTo'],
                $task['assignAssignedTo']
            ];

            // Fill activity columns correctly
            foreach ($activityHeaders as $activityId => $title) {
                $row[] = $task['activities'][$activityId] ?? '---';
            }

            $rows[] = $row;
        }

        /* ============================================
        FINAL JSON RESPONSE
        ============================================ */

        return $this->response->setJSON([
            'success' => true,
            'headers' => $headers,
            'result'  => $rows
        ]);
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

        $reportResult = $reportModel->getReports(
            $search,
            $filter,
            $startDate,
            $endDate,
            $prounit
        );
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
                    'storeName'    => $repo['store_name'],
                    'oldStoreName' => $repo['oldstore_name'],
                    'date'         =>  date('Y-m-d', strtotime($repo['created_at'])),
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

        $header = ['SL NO', 'CODE', 'STORE NAME', 'OLD NAME', 'DATE','TASK','ALLOCATED TO', 'ASSIGNED TO'];

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
                    if ($act['taskStatus'] == 'Completed' && $act['activityStatus'] == 'completed') {
                        $taskStaffActivityModel
                            ->where([
                                'task_activity_id' => $activityId,
                                'task_id'          => $task['taskId']
                            ])
                            ->set(['commet_status' => 2])
                            ->update();
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


}