<?php namespace App\Controllers;

class Home extends BaseController
{
    protected $db;
    protected $sess;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->sess = \Config\Services::session();
    }

    public function index()
	{
		return view('welcome_message');
	}

	//--------------------------------------------------------------------
    public function createUserManagement(){

	    $this->dropUserManagement();
        $forge = \Config\Database::forge();


        $fields = [

            'description' => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
            ],
            'name'       => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
            ],

        ];


        $forge->addField('id');
        $forge->addField($fields);
        $forge->createTable('ic_groups', TRUE);


        $fields = [

            'group_id' => [
                'type'           => 'INT',
                'constraint'     => '5',
                'default'        => '3'
            ],
            'name'       => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
            ],
            'email'      => [
                'type'           =>'VARCHAR',
                'constraint'     => '100',
                'unique'         => true,
            ],
            'pass' => [
                'type'           => 'VARCHAR',
                'constraint' => '100',
            ],
        ];

        $forge->addField($fields);
        $forge->addField('id');
        $forge->addForeignKey('group_id','ic_groups','id');
        $forge->createTable('ic_users', TRUE);


        $fields = [

            'name' => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
            ],
        ];

        $forge->addField('id');
        $forge->addField($fields);
        $forge->createTable('ic_permissions', TRUE);



        $fields = [

            'group_id' => [
                'type'           => 'INT',
                'constraint'     => '9',
            ],

            'permission_id' => [
                'type'           => 'INT',
                'constraint'     => '9',
            ],
        ];
//
        $forge->addField($fields);
        $forge->addField('id');
        $forge->addForeignKey('group_id','ic_groups','id');
        $forge->addForeignKey('permission_id','ic_permissions','id');
        $forge->createTable('ic_groups_permissions', TRUE);

    }


    public function dropUserManagement()
    {
        $forge = \Config\Database::forge();
        $forge->dropTable('ic_users',TRUE);
        $forge->dropTable('ic_groups',TRUE);
        $forge->dropTable('ic_permissions',TRUE);
        $forge->dropTable('ic_groups_permissions',TRUE);
    }

    public function addGroups()
    {
        $data = [
            [
                'name' => "Administrator",
                'description' => 'Administrator Can Manage Everything',
            ],
            [
                'name' => "Editor",
                'description' => 'Editor Can Manage Something',
            ],
            [
                'name' => "User",
                'description' => 'Administrator Can Manage Nothing',
            ],

        ];
        foreach ($data as $key => $value):
            $builder = $this->db->table('ic_groups');
            $builder->insert($value);
        endforeach;
    }


    public function addAdmin() {

        $data = [
            'name' => "Administrator",
            'email' => 'admin@example.ge',
            'pass' => hash('sha256', 'default'),
            'group_id' => 1,
        ];
        $builder = $this->db->table('ic_users');
        $builder->insert($data);

    }

    public function login()
    {
        $builder = $this->db->table('ic_users');
        $builder->where("email",'admin@example.ge');
        $admin = $builder->get()->getRow();

        $request = service('request');
        //TODO: login with post
        $pass = $request->getPost('pass');
        $email = $request->getPost('email');


        $pass = 'default';
        $email = 'admin@example.ge';


        if (hash('sha256', $pass) == $admin->pass && $admin->email == $email) {
            $this->sess->set("user", $admin->name);
            $this->sess->set("email", $admin->email);
            $this->sess->set("group_id", $admin->group_id);
            $this->sess->set("logedin", 1);
            print("User '{$this->sess->get('user')}' logged in");
        } else {
            var_dump(hash('sha256', $pass) == $admin->pass && $admin->email == $email);
        }


    }

    public function logout() {
        $this->sess->destroy();
        return redirect()->to(env("app.baseURL"));
    }

    public function addUser()
    {
        $request = service('request');
        //TODO: login with post
        $pass = $request->getPost('pass');
        $email = $request->getPost('email');
    }

}
