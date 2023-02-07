<?php


class Home extends Controller
{
    private $header;
    private $utilities;

    public function __construct()
    {
        $this->header = new headers();
        $this->utilities = new Utilities();
    }
    public function index($a = '', $b = '', $c = '')
    {
        echo 'hello';
    }
    public function test($a = '', $b = '', $c = '')
    {
        $model = new Model('utilisateur');
        $model->insert(
            array(
                'identifiant' => 'zzz',
                'nom' => 'bouzhar',
                'prenom' => 'abdallah',
                'numero_tel' => '0649600623'
            )
        );
        showd($model->status);
        echo 'from test';
    }

    public function all()
    {
        $this->header->init("GET");
        $this->header->status(200, "OK");
        $model = new Model('utilisateur');
        $data = $model->findAll();

        echo json_encode($data);
    }
}
