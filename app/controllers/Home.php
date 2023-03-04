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
        $model = new Model('utilisateur');

        $this->header->init("POST");
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === "POST") {

            $data = json_decode(file_get_contents('php://input'), true);
            $identifiant = $this->utilities->randomStrGenerator();
            $data['identifiant'] = $identifiant;

            $model->insert($data);

            if (isset($model->status->exception)) {
                
                $pattern = "/for key '(.+?)'/";
                preg_match($pattern, $model->status->exception, $matches);
                $constraint_name = $matches[1];
                
                $this->header->status(409, "not created");
                echo json_encode(
                    [
                        "message" => "exception",
                        "errorIngo" => $model->status->exception,
                        "constraint" => $matches[1]
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
    public function login()
    {
        $model = new Model('utilisateur');

        $this->header->init("POST");
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === "POST") {

            $data = json_decode(file_get_contents('php://input'), true);

            $user = $model->where($data);


            if (isset($model->status->exception)) {
                $this->header->status(200, "error");
                echo json_encode(
                    [
                        "message" => "exception",
                        "errorInfo" => $model->status->exception,

                    ]
                );
                die();
            } elseif ($model->status->affected_rows == 1) {
                $this->header->status(200, "user found");
                echo json_encode(
                    [
                        "message" => "login success",
                        "user" => $user[0]

                    ]
                );
                die();
            } else {
                $this->header->status(200, "not a user");
                echo json_encode(
                    [
                        "message" => "login failed"
                    ]
                );
                die();
            }
        }

        die();
    }

    public function available_Spots_Per_Day()
    {

        $model = new Model('Rendez_vous');

        $this->header->init("POST");
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data = json_decode(file_get_contents('php://input'), true);

            $response['total_available'] = $model->available_Spots_Per_Day($data)[0];
            $response['reserved'] = $model->reserved_Spots_Per_Day($data);
            $response['working'] = $model->working_houres($data)[0];
            echo json_encode($response);

            die();
        }
    }

    public function reserved_Spots_Per_Day()
    {

        $model = new Model('Rendez_vous');

        $this->header->init("POST");
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data = json_decode(file_get_contents('php://input'), true);
            unset($data['jour']);

            echo json_encode($model->reserved_Spots_Per_Day($data));


            die();
        }
    }
    public function working_hours()
    {

        $model = new Model('horaires');

        $this->header->init("POST");
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data = json_decode(file_get_contents('php://input'), true);
            unset($data['date_jour']);

            echo json_encode($model->working_houres($data)[0]);


            die();
        }
    }

    public function makeReservation()
    {
        $model = new Model('Rendez_vous');

        $this->header->init("POST");
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === "POST") {

            $data = json_decode(file_get_contents('php://input'), true);

            if ($model->pending(['identifiant_utilisateur' => $data['identifiant_utilisateur']]) > 0) {
                $this->header->status(201, "refused");
                echo json_encode(
                    [
                        "message" => "you already has a pending reservation",
                        "status" => $model->status,

                    ]
                );
                die();
            }


            $model->insert($data);

            if (isset($model->status->exception)) {

                $this->header->status(409, "not created");
                echo json_encode(
                    [
                        "message" => "exception",
                        "status" => $model->status,

                    ]
                );
                die();
            } else {
                if ($model->status->success) {
                    $reservation = $model->where(['id' => $model->status->last_insert_id]);
                    $this->header->status(201, "Created");
                    echo json_encode([
                        "message" => "reservation created",
                        "status" => $model->status,
                        "reservation" => $reservation[0]
                    ]);
                    die();
                }
            }
        }

        die();
    }

    public function updateReservation()
    {
        $model = new Model('Rendez_vous');

        $this->header->init("POST");
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data = json_decode(file_get_contents('php://input'), true);
            if ($data['old']['identifiant_utilisateur'] != $data['new']['identifiant_utilisateur']) {
                $this->header->status(201, "refused");
                echo json_encode(
                    [
                        "message" => "credentials error",
                        "status" => $model->status,

                    ]
                );
                die();
            }


            $model->update($data['old']['id'], $data['new']);

            if (isset($model->status->exception)) {

                $this->header->status(409, "not updated");
                echo json_encode(
                    [
                        "message" => "exception",
                        "status" => $model->status,

                    ]
                );
                die();
            } else {
                if ($model->status->success) {
                    $reservation = $model->where(['id' => $data['old']['id']]);
                    $this->header->status(201, "updated");
                    echo json_encode([
                        "message" => "reservation updated",
                        "status" => $model->status,
                        "reservation" => $reservation[0]
                    ]);
                    die();
                }
            }
        }

        die();
    }

    public function getUserReservation()
    {
        $model = new Model('Rendez_vous');

        $this->header->init("POST");
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data = json_decode(file_get_contents('php://input'), true);
            $data['etat'] = "pending";
            $response = $model->where($data);
            if (isset($model->status->exception)) {

                $this->header->status(409, "error");
                echo json_encode(
                    [
                        "message" => "error",
                        "status" => $model->status,

                    ]
                );
                die();
            } else {
                if ($model->status->affected_rows == 0) {
                    $this->header->status(201, "empty");
                    echo json_encode([
                        "message" => "you have no reservation",
                        "status" => $model->status,

                    ]);
                    die();
                }
            }

            $this->header->status(201, "empty");
            echo json_encode([
                "message" => "one record found",
                "status" => $model->status,
                "data" => $response[0]
            ]);

            die();
        }
    }

    public function cancelReservation()
    {
        $model = new Model('Rendez_vous');

        $this->header->init("POST");
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data = json_decode(file_get_contents('php://input'), true);
            $model->delete($data['identifiant_utilisateur'], 'identifiant_utilisateur');

            if ($model->status->affected_rows > 0) {
                echo json_encode([
                    "message" => "deleted successefully"
                ]);
            } else {
                echo json_encode([
                    "message" => "something went wrong"
                ]);
            }
            die();
        }
    }
}
