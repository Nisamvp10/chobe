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

    function getdataFromId($id=false) {
        if(!haspermission(session('user_data')['role'],'create_project_unit')) {
             return $this->response->setJSON(['success' => false, 'message' => 'Permission Denied']);
        }
        $id = decryptor($id);
        if($id) {
            $projectUnit = $this->projectUnitModel->where('id',$id)->get()->getRow();
            return $this->response->setJSON(['success' => true, 'result' => $projectUnit]);

        }
        return $this->response->setJSON(['success' => false, 'message' => 'Projectunit Not Found']);

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
            //'oracle_code'   => 'required',
            //'polaris_code'   => 'required',
            'contact_number'   => 'required',
            'client'   => 'required',
            'start_date'   => 'required',
            'rm'   => 'required',
            'store_manager'   => 'required',
            'allocated_to'     => 'required',
            'allocated_date'     => 'required',
            //'allocatedType'     => 'required',
            'rm_mail'  => 'required|min_length[2]',
            //'status'  => 'required',{allocatedType: "The allocatedType field is required."}
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

        $id       = $this->request->getVar('projectId');

        $allocated_to   = $this->request->getPost('allocated_to');
        $allocated_date = $this->request->getPost('allocated_date');
        $allocated_type = 1;// $this->request->getPost('allocatedType');
        $assigned_to    = $this->request->getPost('assigned_to');
        $assigned_date  = $this->request->getPost('assigned_date');
        $assigned_type  = 2;//$this->request->getPost('assignedType'); 


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
            'allocated_to'  =>  $allocated_to,
            'allocated_date'    =>  $allocated_date,
            'allocated_type'    =>  $allocated_type,
            'assigned_to'   =>  $assigned_to,
            'assigned_date' =>  $assigned_date,
            'assigned_type',    $assigned_type,
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

        $builder = $this->projectUnitModel->select('project_unit.id,project_unit.store,project_unit.oldstore_name,project_unit.oracle_code,c.name as clientName,project_unit.status as is_active,
        project_unit.polaris_code,project_unit.rm_mail,project_unit.contact_number,project_unit.start_date,project_unit.contact_number,
        m.name as manager,rm.name as rm,')
        ->join('clients as c', 'c.id = project_unit.client_id', 'left')
        ->join('users as m', 'm.id = project_unit.manager_id', 'left')
        ->join('users as rm', 'rm.id = project_unit.regional_manager_id', 'left');
        if($filter !=='all'){
           $builder->where('c.id',$filter);
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

    // FIX: Use data rows only
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
        if (empty($rowData[1]) || empty($rowData[3]) || empty($rowData[4]) || empty($rowData[7]) || empty($rowData[9]) || empty($rowData[10])) {
            $failedRows[] = $row;
            continue;
        }

        $insertData[] = [
            'store'               => $rowData[1],  // Name
            'oldstore_name'       => $rowData[2] ?? null,
            'oracle_code'         => $rowData[3],
            'polaris_code'        => $rowData[4],
            'rm_mail'             => $rowData[5] ?? null,
            'contact_number'      => $rowData[6] ?? null,
            'client_id'           => $rowData[7] ?? null,
            'start_date'          => !empty($rowData[8]) ? date('Y-m-d', strtotime($rowData[8])) : null,
            'regional_manager_id' => $rowData[9] ?? null,
            'manager_id'          => $rowData[10] ?? null,
            'allocated_to'        => $rowData[11] ?? null,
            'allocated_date'      => !empty($rowData[12]) ? date('Y-m-d', strtotime($rowData[12])) : null,
            'allocated_type'      => $rowData[13] ?? 1,
            'assigned_to'         => $rowData[14] ?? null,
            'assigned_date'       => !empty($rowData[15]) ? date('Y-m-d', strtotime($rowData[15])) : null,
            'assigned_type'       => $rowData[16] ?? 1,
            'status'              => 1,
            'project_unit_type'   => 1, 
        ];
    }
    if (!empty($insertData)) {
       
        if($this->projectUnitModel->insertBatch($insertData)) {
                return $this->response->setJSON([
                'success'     => true,
                'message'     => 'Bulk upload completed',
                'total_rows'  => $highestRow,
                'inserted'    => count($insertData),
                'failed_rows' => $failedRows
            ]);
        }
        
    }

    // return $this->response->setJSON([
    //     'success'     => false,
    //     'message'     => 'Bulk upload completed',
    //     'total_rows'  => $highestRow,
    //     'inserted'    => count($insertData),
    //     'failed_rows' => $failedRows
    // ]);
}

}