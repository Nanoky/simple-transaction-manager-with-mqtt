const express = require("express");
const mqtt = require("mqtt");
const debug_logs = require("debug")("logs");
const debug_error = require("debug")("error");
const compression = require("compression");
const axios = require("axios");

const app = express();
const port = 8080;
const client = mqtt.connect("mqtt://broker.hivemq.com");


// System setting

var mode = "1"; // 0 for register and 1 for payment
var current_account = null;

const transact_url = "http://localhost:8081";
const account_url = "http://localhost:8082";


// MQTT

const reader_connected_topic = "uppro/connected";
const reader_pay_topic = "uppro/pay";
const reader_new_topic = "uppro/new";

const server_mode_topic = "uppro/mode";
const server_bill_topic = "uppro/bill";
const server_saved_topic = "uppro/saved";

const mqtt_false = "0";
const mqtt_true = "1";

client.on("connect", () => {
    client.subscribe(reader_connected_topic);
    client.subscribe(reader_pay_topic);
    client.subscribe(reader_new_topic);
});

client.on("message", (topic, message) => {

    message = message.toString();

    switch (topic) {
        case reader_connected_topic:
            sendMode(message);
            break;
        
        case reader_pay_topic:
            handlePayment(message);
            break;

        case reader_new_topic:
            handleRegister(message);
            break;
    
        default:
            break;
    }

});


// Routes and request configuration 

app.use(compression());


app.use((req, res, next) => {

    res.setHeader("Access-Control-Allow-Origin", "*");
    res.setHeader("Access-Control-Allow-Methods", "GET, POST");

    next();

});

// Handle web client command

app.post("/account", (req, res, next) => {

    current_account = req.body.id
    res.end();

});

app.use((req, res, next) => {
    res.end();
});

app.listen(port);

function webResponse(res, success = true, data = [], message = "")
{
    res.json({
        success : success,
        data : data,
        message : message
    });

    res.end();
}

function sendMode(message)
{
    debug_logs("Connected : " + message);
    client.publish(server_mode_topic, mode);
}

function handlePayment(message)
{
    // send http request to transact API
    debug_logs("Payment : " + message);
    axios.post(transact_url, {
        code : message
    }).then((data) => {
        data = data.data;
        debug_logs(data)
        if (data.success == true)
        {
            client.publish(server_bill_topic, mqtt_true);
        }
        else
        {
            client.publish(server_bill_topic, mqtt_false);
        }
    }).catch((error) => {
        debug_error(error);
        client.publish(server_bill_topic, mqtt_false);
    });
}

function handleRegister(message)
{
    // send http request to account API
    axios.put(account_url + "/set/card", {
        id_account : current_account,
        code : message
    }).then((message) => {
        if (message == true)
        {
            client.publish(server_saved_topic, mqtt_true);
        }
        else
        {
            client.publish(server_saved_topic, mqtt_false);
        }
    }).catch((error) => {
        client.publish(server_saved_topic, mqtt_false);
    });
}