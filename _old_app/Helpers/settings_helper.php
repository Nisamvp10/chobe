<?php
if(!function_exists('saveSettings')){
    function saveSettings($name,$value){
        $db = \Config\Database::connect();
        $builder = $db->table('app_settings');
        $data = [
            'name'  => $name,
            'value' => $value,
            'status'    => 1,
        ];
        $query = $builder->select('*')->where('name',$name)->get();
        if($query->getNumRows() < 1){
            return $builder->insert($data);
        }else{
            return $builder->where('name',$name)->update($data);
        }
    }
}

if(!function_exists('getappdata')){
    function getappdata($name){
        $db = \Config\Database::connect();
        $builder = $db->table('app_settings');
        $query = $builder->select('value')->where('name',$name)->get();
        if($query->getNumRows() < 1){
            return false;
        }
        $data = $query->getRow();
        return  $data->value ? $data->value : false;
    }
}
if(!function_exists('encryptor'))
{
    function encryptor($id) {
        $key = 'my_secret_key_32_bytes_long_!!!'; // 32 bytes for AES-256
        $iv  = substr(hash('sha256', 'my_iv_secret'), 0, 16); 
        return base64_encode(openssl_encrypt($id, 'AES-256-CBC', $key, 0, $iv));
    }
}

if(!function_exists('decryptor'))
{
    function decryptor($encrypted_id) {
        $key = 'my_secret_key_32_bytes_long_!!!';
        $iv  = substr(hash('sha256', 'my_iv_secret'), 0, 16);
        return openssl_decrypt(base64_decode($encrypted_id), 'AES-256-CBC', $key, 0, $iv);
    }
}
