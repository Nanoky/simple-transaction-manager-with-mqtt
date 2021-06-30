<?php

    use Goa\Http\Base\Request as BaseRequest;
    use Ngbin\Framework\App;
    use Ngbin\Framework\Entity\Request;
    use Ngbin\Framework\Entity\Response;
    use Ngbin\Framework\Formatter\ToJSON;

    include_once "vendor/autoload.php";
    include_once "vendor/nanok/simple-http-request/Request.php";

    const db_url = "http://localhost:8083/account";

    $app = new App();


    $app->put("/set/card", function (Request $request) {

        $response = BaseRequest::put(db_url . "/code", [
            "matricule" => $request->body["id"],
            "code" => $request->body["card"]
        ]);

        return new Response($response, new ToJSON());
    });

    $app->post("/", function (Request $request) {
        return new Response([], new ToJSON());
    });

    $app->get("/card/:id", function (Request $request) {

        $response = BaseRequest::get(db_url . "/" . $request->params["id"]);
        return new Response($response, new ToJSON());
    });

    $app->run();

?>