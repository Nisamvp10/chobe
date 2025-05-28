<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CategoryModel;

class Category extends Controller{
    protected $categoryModel;

    function __construct(){
         $this->categoryModel = new CategoryModel();
    }

    function index(){
        if(!haspermission(session('user_data')['role'],'view_category')) {
            $page ="Permission Denied";
        }else{
            $page = "Category";
        }
        return view('admin/category/category',compact('page'));
    }
    function create($id =false)
    {
        $page = "Edit Category" ; 
        $id = decryptor($id);
        $data = $this->categoryModel->where(['id'=> $id,'is_active' =>1])->first();
        return view('admin/category/edit',compact('page','data'));
    }

    function save(){
        if(!$this->request->isAJAX())
        {
            return $this->response->setJSON(['success' => false,'message' => ' Invalid Request']);
        }
        if(!haspermission(session('user_data')['role'],'create_category')) {
            return $this->response->setJSON(['success' => false,'message' => ' Permission Denied']);
        }
        $validSuccess = false;
        $validMsg = '';

        $rules = [
            'category' => 'required|min_length[3]|max_length[100]',
        ];

        if(!$this->validate($rules)){
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        $category = $this->request->getVar('category');
        $id = decryptor($this->request->getVar('categoryId'));

        $data = [
            'category' => $category
        ];
        
        if($id)
        {
            if($this->categoryModel->update($id, $data)){

                $validSuccess = true;
                $validMsg = "Updated successfully!";
            }else{
                $validMsg = 'something went wrong Please Try again';
            }
        }else{
            if($this->categoryModel->insert($data)){

                $validSuccess = true;
                $validMsg = "New Category Added";
            }else{
                $validMsg = 'something went wrong Please Try again';
            }
        }

        return $this->response->setJSON([
            'success' => $validSuccess,
            'message' => $validMsg,
        ]);
    }
    function categoryList(){

        if(!$this->request->isAJAX()){
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid Request']);
        }
        if(!haspermission(session('user_data')['role'],'view_category')) {
            return $this->response->setJSON(['success' => false,'message' => ' Permission Denied']);
        }
        $search = $this->request->getVar('search');
        $filter = $this->request->getVar('filter');

        $builder = $this->categoryModel->select('id,category,is_active')->orderBy('id DESC');

        if($filter !=='all'){
            $builder->where('is_active',$filter);
        }

        if(!empty($search))
        {
            $builder->groupStart()
                ->like('category',$search)
                ->groupEnd();
        }

        $categories = $builder->findAll();
        foreach($categories as &$category){
            $category['encrypted_id'] = encryptor($category['id']);
        }

        return $this->response->setJSON([
            'success' => true,
            'categories' => $categories,
        ]);
    }
    function delete()
    {
        $id = decryptor($this->request->getVar('id'));
        $validSuccess = false;
        $validMsg = "oops! Item Not Valid ";
        if($id)
        {
            $branch = $this->categoryModel->find($id);
            if($branch){
                //check assigned any staff and appoint ment in the branch 
                if( $this->categoryModel->update($id,['is_active'=>0])){
                    $validSuccess = true;
                    $validMsg = 'Category Inactive successfully!';
                }else{
                    $validMsg = 'Oops. Please try again.';
                }
            }
        }
        return $this->response->setJson([
            'success' => $validSuccess,
            'message' => $validMsg
        ]);
    }
    function unlock()
    {
        $id = decryptor($this->request->getVar('id'));
        $validSuccess = false;
        $validMsg = "oops! Item Not Valid ";
        if($id)
        {
            $branch = $this->categoryModel->find($id);
            if($branch){
                //check assigned any staff and appoint ment in the branch 
                if( $this->categoryModel->update($id,['is_active'=>1])){
                    $validSuccess = true;
                    $validMsg = 'Category Active successfully!';
                }else{
                    $validMsg = 'Oops. Please try again.';
                }
            }
        }
        return $this->response->setJson([
            'success' => $validSuccess,
            'message' => $validMsg
        ]);
    }
}
