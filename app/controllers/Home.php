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
        $str = $this->utilities->randomStrGenerator();
        echo $str;
    }
    public function signup()
    {

        $this->header->init("POST");
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $data = json_decode(file_get_contents('php://input'), true);
            $data['identifiant'] = $identifiant = $this->utilities->randomStrGenerator();
            $model = new Model('utilisateur');
            $model->insert($data);
            if (isset($model->status->exception)) {
                $this->header->status(200, "not created");
                echo json_encode(
                    [
                        "message" => "exception",
                        "errorIngo" => $model->status->exception
                    ]
                );
                die();
            } else {
                if ($model->status->success) {

                    $this->header->status(201, "Created");
                    echo json_encode([
                        "message" => "client created",
                        "token" => $identifiant,
                    ]);
                    die();
                }
            }
        }

        die();
    }

    
}
