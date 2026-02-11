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
        $activityHeaders = [];

        /** STEP 1: GROUP BY TASK **/
        foreach ($reportResult as $repo) {
            $taskId = $repo['taskId'];

            if (!isset($groupedTasks[$taskId])) {
                $groupedTasks[$taskId] = [
                    'oracleCode'   => $repo['oracle_code'],
                    'storeName'    => $repo['store_name'],
                    'oldStoreName' => $repo['oldstore_name'],
                    'date' => date('Y-m-d',strtotime($repo['created_at'])),
                    'activities'   => []
                ];
            }

            $activityIndex = count($groupedTasks[$taskId]['activities']);

            // Dynamic header
            if (!isset($activityHeaders[$activityIndex])) {
                $activityHeaders[$activityIndex] =
                    $repo['activity_title'] ; //. ' (ID:' . $repo['activity_id'] . ')'
            }

            $groupedTasks[$taskId]['activities'][] =
                !empty($repo['last_comment'] || $repo['last_comment'] != "Nill")
                    ? $repo['last_comment']
                    : 'Not commented';
        }

        /** STEP 2: FIND MAX ACTIVITIES **/
        $maxActivities = 0;
        foreach ($groupedTasks as $task) {
            $maxActivities = max($maxActivities, count($task['activities']));
        }

        /** STEP 3: BUILD HEADERS **/
        $headers = ['SL NO', 'CODE', 'STORE NAME', 'OLD NAME','DATE'];
        for ($i = 0; $i < $maxActivities; $i++) {
            $headers[] = $activityHeaders[$i] ?? 'Activity ' . ($i + 1);
        }

        /** STEP 4: BUILD TABLE ROWS **/
        $rows = [];
        $sl = 1;

        foreach ($groupedTasks as $task) {
            $row = [
                $sl++,
                $task['oracleCode'],
                $task['storeName'],
                $task['oldStoreName'],
                $task['date'],
            ];

            // Activity values
            foreach ($task['activities'] as $activity) {
                $row[] = $activity;
            }

            // Fill empty cells
            for ($i = count($task['activities']); $i < $maxActivities; $i++) {
                $row[] = '';
            }

            $rows[] = $row;
        }

        return $this->response->setJSON([
            'success' => true,
            'headers' => $headers,
            'result'    => $rows
        ]);
    }



    public function generateReport()
    {
        // IMPORTANT: clear output buffer
        if (ob_get_length()) ob_end_clean();

        $reportModel = new ReportModel();
        $taskStaffActivityModel = new TaskStaffActivityModel();

        $search    = $this->request->getGet('search');
        $filter    = $this->request->getGet('filter');
        $startDate = $this->request->getGet('startDate');
        $endDate   = $this->request->getGet('endDate');
        $prounit   = $this->request->getGet('prounit');

        $reportResult = $reportModel->getReports($search, $filter, $startDate, $endDate, $prounit);

        $resultData = [];
        $activityHeaders = [];

        // GROUP DATA
        foreach ($reportResult as $repo) {
            $taskId = $repo['taskId'];
         
            if (!isset($resultData[$taskId])) {
                $resultData[$taskId] = [
                    'taskId'       => $taskId,
                    'storeName'    => $repo['store_name'],
                    'oldStoreName' => $repo['oldstore_name'],
                    'oracleCode'   => $repo['oracle_code'],
                    'allocated_to' => $repo['allocated_to'],
                    'assigned_to'  => ($repo['allocated_to_id'] == $repo['assigned_to_id'] ? NULL : $repo['assigned_to']), //$repo['assigned_to']
                    'activities'   => []
                ];
            }

            $idx = count($resultData[$taskId]['activities']);

            if (!isset($activityHeaders[$idx])) {
                $activityHeaders[$idx] = $repo['activity_title'];
            }

            $resultData[$taskId]['activities'][] = [
                'activity_id'    => $repo['activity_id'],
                'comment'        => $repo['last_comment'] ?: 'Not commented',
                'taskStatus'     => $repo['taskStatus'],
                'activityStatus' => $repo['activityStatus']
            ];
        }

        // MAX ACTIVITY COUNT
        $maxActivities = 0;
        foreach ($resultData as $t) {
            $maxActivities = max($maxActivities, count($t['activities']));
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // HEADER
        $header = ['SL NO', 'CODE', 'STORE NAME', 'OLD NAME','ALLOCATED TO','ASSIGNED TO'];
        for ($i = 0; $i < $maxActivities; $i++) {
            $header[] = $activityHeaders[$i] ?? 'Activity '.($i+1);
        }

        $sheet->fromArray($header, null, 'A1');

        // STYLE HEADER
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle("A1:{$highestColumn}1")->getFont()->setBold(true);
        $sheet->getStyle("A1:{$highestColumn}1")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // DATA
        $rowNo = 2;
        $sl = 1;

        foreach ($resultData as $task) {
          
            $row = [
                $sl++,
                $task['oracleCode'],
                $task['storeName'],
                $task['oldStoreName'],
                $task['allocated_to'],
                $task['assigned_to']
            ];

            foreach ($task['activities'] as $act) {

                if ($act['taskStatus'] == 'Completed' && $act['activityStatus'] == 'completed') {
                    $taskStaffActivityModel
                        ->where([
                            'task_activity_id' => $act['activity_id'],
                            'task_id'          => $task['taskId']
                        ])
                        ->set(['commet_status' => 2])
                        ->update();
                }

                $row[] = $act['comment'];
            }

            while (count($row) < count($header)) {
                $row[] = '';
            }

            $sheet->fromArray($row, null, 'A'.$rowNo++);
        }

        // AUTO SIZE BASED ON CONTENT (SAFE)
        $highestColumnIndex = Coordinate::columnIndexFromString($sheet->getHighestColumn());

        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $columnLetter = Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
            $sheet->getStyle($columnLetter)->getAlignment()->setWrapText(true);
        }

        // OUTPUT
        $filename = 'task_report_'.date('Y-m-d_H-i-s').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }


}