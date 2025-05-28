<?php

if (!function_exists('updateImage')) {
    function updateImage($url)
    {
       $url =  str_replace(base_url(), '', $url);
       
        if (file_exists($url)) {
            unlink($url);
        }
    }
}

if(!function_exists('getStore')) {
    function getStore() {
        $db = \Config\Database::connect();
        $userRole = session('user_data')['role'];
        $userId = session('user_data')['id'];
        $branchId = '';

        if($userRole != 1) {

            $queryStore = $db->table('users as u')
                    ->select('u.store_id')
                    ->where('id',$userId)->get()->getRow();
            $queryBranch = $db->table('branches')
                    ->select('id')
                    ->where('id',$queryStore->store_id)->get()->getRow();
            
            $branchId =  $queryBranch->id;
        
        }                
        return $branchId ?? false;
    }
}
