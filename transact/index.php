<?php

    use Goa\Http\Base\Request as BaseRequest;
    use Ngbin\Framework\App;
    use Ngbin\Framework\Entity\Request;
    use Ngbin\Framework\Entity\Response;
    use Ngbin\Framework\Formatter\ToJSON;

    include_once "vendor/autoload.php";
    include_once "vendor/nanok/simple-http-request/Request.php";

    const db_url = "http://localhost:8083/transact";
    const account_url = "http://localhost:8082";

    $app = new App();

    $app->post("/", function (Request $request) {

        error_log(json_encode($request->body));

        $response = BaseRequest::get(account_url . "/card/" . $request->body["code"]);

        if ($response->success && !empty($response->data))
        {
            $response = BaseRequest::post(db_url, [
                "code" => $request->body["code"],
                "datetime" => date("c")
            ]);
        }
        else
        {
            $response->success = false;
        }

        return new Response($response, new ToJSON());
    });

    $app->get("/:id", function (Request $request) {

        $response = BaseRequest::get(db_url . "/" . $request->params['id']);
        return new Response($response, new ToJSON());
    });

    $app->run();

?>