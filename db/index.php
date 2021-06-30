<?php

use Models\DB;
use Ngbin\Framework\App;
    use Ngbin\Framework\Entity\Request;
    use Ngbin\Framework\Entity\Response;
    use Ngbin\Framework\Formatter\ToJSON;

    include_once "vendor/autoload.php";
    include_once "database/DB.php";
    include_once "database/ModelFunction.php";
    include_once "database/ModelInterface.php";
    include_once "database/Model.php";
    include_once "models/transact.php";
    include_once "models/account.php";

    $config = [
        "sgbd" => "sqlite",
        "host" => "localhost",
        "dbname" => "uppro",
        "username" => "uppro_admin",
        "password" => "123456789",
        "file" => "uppro"
    ];

    $app = new App();

    $app->post("/transact", function (Request $request) {

        $response = [
            "success" => false,
            "message" => "",
            "data" => []
        ];

        try {
            $model = new Transact($request->body["code"], $request->body["datetime"]);
            $model->save();
            $response["success"] = true;
        } catch (\Exception $th) {
            $response["message"] = $th->getMessage();
        }

        return new Response($response, new ToJSON());

    });

    $app->post("/transact/:id", function (Request $request) {

        $response = [
            "success" => false,
            "message" => "",
            "data" => []
        ];

        try {
            $model = new Transact($request->params["id"], 0);
            $response["data"] = $model->all();
            $response["success"] = true;
        } catch (\Exception $th) {
            $response["message"] = $th->getMessage();
        }

        return new Response($response, new ToJSON());

    });

    $app->get("/account/:id", function (Request $request) {

        $response = [
            "success" => false,
            "message" => "",
            "data" => []
        ];

        try {
            $model = new Account();
            $response["data"] = $model->findById([
                "code" => $request->params["id"]
            ]);
            $response["success"] = true;
        } catch (\Exception $th) {
            $response["message"] = $th->getMessage();
        }

        return new Response($response, new ToJSON());

    });

    $app->get("/", function (Request $request) {

        global $config;

        $response = [
            "success" => false,
            "message" => "",
            "data" => []
        ];

        try {
            $model = new Account();
            $accounts = $model->all();

            error_log(json_encode($accounts));

            $db = new DB($config);

            foreach ($accounts as $key => $value) {
                
                $value = $value->code;

                $count = $db->select("SELECT COUNT(code) as count, date FROM transact WHERE code=:code ORDER BY date DESC LIMIT 1", [
                    'code' => $value["code"]
                ]);

                $count = $count[0];
                error_log(json_encode($count));

                $response["data"][] = [
                    "matricule" => $value["matricule"],
                    "nom" => $value["name"] . " " . $value["firstname"],
                    "date" => date("d m Y", strtotime($count["date"])),
                    "heure" => date("H:i", strtotime($count["date"])),
                    "nb" => $count["count"] 
                ];
            }
            
            $response["success"] = true;
        } catch (\Exception $th) {
            $response["message"] = $th->getMessage();
        }

        $r = new Response($response, new ToJSON());

        $r = addHeaders($r);

        return $r;

    });

    $app->run();

    function addHeaders($response)
    {
        $response->setHeader("Access-Control-Allow-Origin", "*");
        $response->setHeader("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE");
        $response->setHeader("Content-Type: application/json", "charset=UTF-8");
        $response->setHeader("Access-Control-Max-Age", "3600");
        $response->setHeader("Access-Control-Allow-Headers", "Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        return $response;
    }

?>