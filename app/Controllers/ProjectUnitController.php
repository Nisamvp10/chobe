<?php
namespace App\Controllers;

use CodeIgniter\Controller;

use App\Models\ProjectunitModel;
use App\Models\BranchesModel;
use App\Models\ClientsModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProjectUnitController extends Controller
{
    protected $projectUnitModel;
    protected $branchModel;
    protected $clientsModel;

    function __construct() {
        $this->projectUnitModel = new ProjectunitModel();
        $this->branchModel = new BranchesModel();
        $this->clientsModel = new ClientsModel();
    }
    public function index()
    {
        $page = (!hasPermission('','view_project_unit')) ?  lang('Custom.accessDenied') : 'Project Unit';
        $stores = $this->clientsModel->where('status',1)->find();
        return view('admin/project_unit/index',compact('page','stores'));
    }

    function save() {
        $validStatus = false;
        $validStatus = '';
        if(!$this->request->isAJAX()){
            return $this->response->setJSON(['success'=> false, 'message' => 'invalid Request']);
        }
        if(!haspermission(session('user_data')['role'],'create_project_unit')) {
             return $this->response->setJSON(['success' => false, 'message' => 'Permission Denied']);
        }

        $rules = [
            'store'    => 'required|min_length[3]|max_length[100]',
            'old_name'  => 'required|min_length[2]',
            'oracle_code'   => 'required',
            'polaris_code'   => 'required',
            'contact_number'   => 'required',
            'client'   => 'required',
            'start_date'   => 'required',
            'rm'   => 'required',
            'store_manager'   => 'required',
            'rm_mail'  => 'required|min_length[2]',
            //'status'  => 'required',
        ];

        if(!$this->validate($rules))
        {
            return $this->response->setJSON([
                'succeass' => false,
                'errors' => $this->validator->getErrors()        
            ]);
        }

        $store   = $this->request->getVar('store');
        $rm_mail = $this->request->getVar('rm_mail');
        $rm = $this->request->getVar(index: 'rm');
        $oldstore = $this->request->getVar('old_name');
        $contactNumber = $this->request->getVar('contact_number');
        $client = $this->request->getVar('client');
        $storeManager = $this->request->getVar('store_manager');
        $startDate = $this->request->getVar('start_date');
        $polaris_code = $this->request->getVar('polaris_code');
        $oracle_code = $this->request->getVar('oracle_code');

        $id       = decryptor($this->request->getVar('branchId'));

        $data = [
            'store' => $store,
            'oldstore_name' => $oldstore,
            'polaris_code'  => $polaris_code,
            'oracle_code'   => $oracle_code,
            'contact_number'  => $contactNumber,
            'rm_mail'       => $rm_mail,
            'client_id'     => $client,
            'manager_id'    => $storeManager,
            'regional_manager_id'   => $rm,
            'start_date'    => $startDate,
            'status'        => 1,
            'project_unit_type' =>1, 
        ];

        if($id){
            if($this->projectUnitModel->update($id, $data)){
                
                $validStatus = true;
                $validMsg = 'Updated successfully!';

            }else{
                
                $validMsg = 'something went wrong Please Try again';
            }
        }else{
            if($this->projectUnitModel->insert($data)){

                $validStatus = true;
                $validMsg = 'New Branch Adedd';

            }else{
                
                $validMsg = 'something went wrong Please Try again';
            }
        }
        return $this->response->setJSON([
            'success' => $validStatus,
            'message' => $validMsg
        ]);
    }

    function list() {
        if(!$this->request->isAJAX()){
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid Request']);
        }
        if(!haspermission('','view_project_unit')) {
            return $this->response->setJSON(['success' => false,'message' => ' Permission Denied']);
        }
        $search = $this->request->getVar('search');
        $filter = $this->request->getVar('filter');

        $builder = $this->projectUnitModel->select('project_unit.id,project_unit.store,project_unit.oldstore_name,project_unit.oracle_code,c.name as clientName,
        project_unit.polaris_code,project_unit.rm_mail,project_unit.contact_number,project_unit.start_date,project_unit.contact_number,
        m.name as manager,rm.name as rm,')
        ->join('clients as c', 'c.id = project_unit.client_id', 'left')
        ->join('users as m', 'm.id = project_unit.manager_id', 'left')
        ->join('users as rm', 'rm.id = project_unit.regional_manager_id', 'left');
        if($filter !=='all'){
          //  $builder->where('project_unit.is_active',$filter);
        }

        if(!empty($search))
        {
            $builder->groupStart()
                ->like('project_unit.store',$search)
                ->groupEnd();
        }

        $projects = $builder->findAll();
        foreach($projects as &$project){
            $project['encrypted_id'] = encryptor($project['id']);
        }

        return $this->response->setJSON([
            'success' => true,
            'projects' => $projects,
        ]);
    }

    //bulk project unit uploader 

public function bulkUpload()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid Request']);
    }

    if (!haspermission(session('user_data')['role'], 'create_project_unit')) {
        return $this->response->setJSON(['success' => false, 'message' => 'Permission Denied']);
    }

    $file = $this->request->getFile('staff_excel');

    if (!$file || !$file->isValid()) {
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid file']);
    }

    $spreadsheet = IOFactory::load($file->getTempName());
    $sheet       = $spreadsheet->getActiveSheet();

    // ✅ FIX: Use data rows only
    $highestRow = $sheet->getHighestDataRow();
    $highestCol = $sheet->getHighestDataColumn();

    $insertData = [];
    $failedRows = [];

    // Start from row 2 (header in row 1)
    for ($row = 2; $row <= $highestRow; $row++) {

        $rowData = $sheet->rangeToArray(
            "A{$row}:{$highestCol}{$row}",
            null,
            true,
            true,
            false
        )[0];

        $rowData = array_map('trim', $rowData);

        // Skip fully empty rows
        if (count(array_filter($rowData)) === 0) {
            continue;
        }

        // Required fields
        if (empty($rowData[1]) || empty($rowData[3]) || empty($rowData[4])) {
            $failedRows[] = $row;
            continue;
        }

        $insertData[] = [
            'store'               => $rowData[1],  // Name
            'oldstore_name'       => $rowData[2] ?? null,
            'oracle_code'         => $rowData[3],
            'polaris_code'        => $rowData[4],
            'email'               => $rowData[5] ?? null,
            'contact_number'      => $rowData[6] ?? null,
            'rm_name'             => $rowData[7] ?? null,
            'start_date'          => !empty($rowData[8]) ? date('Y-m-d', strtotime($rowData[8])) : null,
            'store_manager_name'  => $rowData[9] ?? null,
            'allocated_to'        => $rowData[10] ?? null,
            'allocated_date'      => !empty($rowData[11]) ? date('Y-m-d', strtotime($rowData[11])) : null,
            'assigned_to'         => $rowData[12] ?? null,
            'assigned_date'       => !empty($rowData[13]) ? date('Y-m-d', strtotime($rowData[13])) : null,
            'status'              => 1,
        ];
    }

    if (!empty($insertData)) {
       return $this->response->setJSON([
        'success'     => true,
        'message'     => 'Bulk upload completed',
        'total_rows'  => $highestRow,
        'inserted'    => count($insertData),
        'failed_rows' => $failedRows
    ]);
        // $this->projectUnitModel->insertBatch($insertData);
    }

    return $this->response->setJSON([
        'success'     => false,
        'message'     => 'Bulk upload completed',
        'total_rows'  => $highestRow,
        'inserted'    => count($insertData),
        'failed_rows' => $failedRows
    ]);
}

}