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

            $db = new DB($config);

            foreach ($accounts as $key => $value) {
                
                $count = $db->select("SELECT COUNT(date) as count, MAX(date) as date FROM transact WHERE code=:code", [
                    'code' => $value->code
                ]);

                $response["data"][] = [
                    "matricule" => $value->matricule,
                    "nom" => $value->nom . " " . $value->prenoms,
                    "date" => date("d m Y", strtotime($count["date"])),
                    "heure" => date("H:i", strtotime($count["date"])),
                    "nb" => $count["count"] 
                ];
            }
            
            $response["success"] = true;
        } catch (\Exception $th) {
            $response["message"] = $th->getMessage();
        }

        return new Response($response, new ToJSON());

    });

    $app->run();

?>