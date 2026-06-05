<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => '100'],
            'phone'       => ['type' => 'VARCHAR', 'constraint' => '20'],
            'position'    => ['type' => 'VARCHAR', 'constraint' => '255','NULL' => true],
            'hire_date'   => ['type' => 'DATETIME','NULL'=> true],
            'email'       => ['type' => 'VARCHAR', 'constraint' => '100'],
            'password'    => ['type' => 'VARCHAR', 'constraint' => '255'],
            'role'        => ['type' => 'VARCHAR', 'constraint' => '50'],
            'store_id'    => ['type' => 'INT','null' => true],
            'status'      => ['type' => "ENUM('pending','approved','declined')", 'null' => true],
            'profileimg'  => ['type' => 'TEXT' ,'NULL' => true],
            'booking_status' =>['type'=> 'TINYINT','constraint'=> 1,'default'=> 0,],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');

        //create role Table
        $this->forge->addField([
            'id'        =>  ['type' =>  'INT','unsigned' => true, 'auto_increment' => true],
            'role_name' =>  ['type' =>  'VARCHAR','constraint' => '250']
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('roles');
        //login History
        $this->forge->addField([
            'id'            =>  ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'       =>  ['type' => 'INT','NULL' =>  true],
            'login_time'    =>  ['type' => 'DATETIME','NULL'=> true],
            'ip_address'    =>  ['type' => 'LONGTEXT','NULL'=> true],
            'user_agent'    =>  ['type' => 'TEXT','NULL'    => true],
            'status'        =>  ['type' => "ENUM('success','failed','declined')", 'null' => true],
            'created_at'    =>  ['type' => 'DATETIME','null'    => true,'default' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('login_history');
        //permissions
        $this->forge->addField([
            'id'    => ['type'  =>  'INT','unsigned'    =>  true, 'auto_increment'  =>  true],
            'permission_name'   =>  ['type','VARCHAR', 'constraint' => '250' ],
            'created_at'        =>  ['type' => 'DATETIME','null'    => true,'default' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->createTable('permissions');
        //permission_access
        $this->forge->addField([
            'id'            =>  ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'       =>  ['type' => 'INT','NULL'=>FALSE],
            'role_id'       =>  ['type' => 'INT','NULL'=>FALSE],
            'permission_id' =>  ['type' => 'INT','NULL'=>FALSE],
            'created_by'    =>  ['type' => 'INT','NULL'=>FALSE],
            'created_at'    =>  ['type' => 'DATETIME','null'    => true,'default' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->createTable('permission_access');
        //setttings
        $this->forge->addField([
            'id'    =>  ['type' => 'INT','unsigned' => TRUE, 'auto_increment' => TRUE],
            'name'  =>  ['type' => 'LONGTEXT', 'NULL' => TRUE],
            'value' =>  ['type' => 'LONGTEXT', 'NULL' => TRUE],
            'status'=>  ['type' => 'INT','NULL' => TRUE],
            'created_at'    =>  ['type' => 'DATETIME','null'    => true,'default' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('app_settings');
        //branch
        $this->forge->addField([
            'id'    => ['type' => 'INT','unsigned' => TRUE, 'auto_increment' => true],
            'branch_name'   =>  ['type' => 'VARCHAR','constraint' => 250],
            'location'      =>  ['type' => 'VARCHAR','constraint' => 250],
            'status'        =>  ['type' => "ENUM('active','inactive')", 'null' => true, 'default'   => 'active',],
            'created_at'    =>  ['type' => 'DATETIME','null'    => true,'default' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('branches');
         //services
         $this->forge->addField([
            'id'            => ['type'=> 'INT','unsigned'=> TRUE,'auto_increment' => true],
            'name'          => ['type'=> 'VARCHAR','constraint'=> 255,'null'=> false,],
            'category'      => ['type'=> 'VARCHAR','constraint'=> 100,'null'=> false,],
            'duration'      => ['type'=> 'INT','unsigned'=> true,'null'=> false,],
            'price'         => ['type'=> 'DECIMAL','constraint'=> '10,2','unsigned'=> true,'null'=> false,],
            'description'   => ['type'=> 'TEXT','null'=> false,],
            'image_url'     => ['type'=> 'TEXT','null'=> true,],
            'is_popular'    => ['type'=> 'TINYINT','constraint'=> 1,'default'=> 0,],
            'is_active'     => ['type'=> 'TINYINT','constraint'=> 1,'default'=> 1,],
            'created_at'    => ['type'=> 'DATETIME','default'=> 'CURRENT_TIMESTAMP',],
            'updated_at'    => ['type'=> 'DATETIME','default'=> 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',],
        ]);
        $this->forge->addKey('id', true); 
        $this->forge->createTable('services');
        //category
        $this->forge->addField([
            'id'            => ['type'=> 'INT','unsigned'=> TRUE,'auto_increment' => true],
            'category'      => ['type'=> 'VARCHAR','constraint'=> 255,'null'=> false,],
            'is_active'     => ['type'=> 'TINYINT','constraint'=> 1,'default'=> 1,],
            'created_at'    => ['type'=> 'DATETIME','default'=> 'CURRENT_TIMESTAMP',],
            'updated_at'    => ['type'=> 'DATETIME','default'=> 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',],
        ]);
        $this->forge->addKey('id');
        $this->forge->createTable('categories');
        //specialties
        $this->forge->addField([
            'id'            => ['type'=> 'INT','unsigned'=> TRUE,'auto_increment' => true],
            'staff_id'      => ['type'=> 'INT','null'=> false,],
            'speciality'    => ['type'=> 'INT','null'=> false,],
            'created_at'    => ['type'=> 'DATETIME','default'=> 'CURRENT_TIMESTAMP',],
        ]);
        $this->forge->addKey('id');
        $this->forge->CreateTable('specialties');
    }
    public function down()
    {
        $this->forge->dropTable('users');
        $this->forge->dropTable('roles');
        $this->forge->dropTable('login_history');
        $this->forge->dropTable('permissions');
        $this->forge->dropTable('permission_access');
        $this->forge->dropTable('app_settings');
        $this->forge->dropTable('branches');
        $this->forge->dropTable('services');
        $this->forge->dropTable('categories');
        $this->forge->dropTable('specialties');
    }
}
