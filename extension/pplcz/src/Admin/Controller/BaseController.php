<?php

namespace PPLCZ\Admin\Controller;

use Opencart\Admin\Model\Extension\Pplcz\Normalizer;
use Opencart\Admin\Model\Extension\Pplcz\Shipment;
use Opencart\Admin\Model\Extension\Pplcz\Validator;
use Opencart\System\Engine\Controller;
use Opencart\System\Engine\Registry;

use Opencart\System\Library\Response;
use PPLCZ\Validator\WP_Error;


/**
 * @property-read Validator $model_extension_pplcz_validator
 * @property-read \Opencart\Admin\Model\Extension\Pplcz\Setting $model_extension_pplcz_setting
 * @property-read Normalizer $model_extension_pplcz_normalizer
 * @property-read Shipment $model_extension_pplcz_shipment
 * @property-read Response $response
 */
class BaseController extends Controller
{
    protected function createUrl($route, $params = [], $target = '')
    {
        $params = array_filter($params);
        $user_token = $this->session->data['user_token'];
        $url = "index.php?" . http_build_query(["route" => $route, 'user_token' => $user_token] + $params);
        if ($target) {
            $url .= '#' . urlencode($target);
        }
        return $url;
    }

    protected function validateData($data, $path = "", $wperror = null)
    {
        if (!$wperror)
            $wperror = new WP_Error();

        $this->load->model("extension/pplcz/validator");
        $this->model_extension_pplcz_validator->validate($data, $wperror, $path);
        return $wperror;
    }

    protected function validateDataSendIfError($data, $path = "")
    {
        $wperror = new WP_Error();
        $this->load->model("extension/pplcz/validator");
        $this->model_extension_pplcz_validator->validate($data, $wperror, $path);
        if ($wperror->errors) {
            $this->sendJson($wperror);
            return false;
        }
        return true;
    }

    protected function getData($onlyBody = false)
    {
        $method = strtoupper($this->request->server['REQUEST_METHOD'] ?? 'GET');
        $ctype = $this->request->server['CONTENT_TYPE'] ?? '';
        $raw = file_get_contents('php://input') ?: '';
        if (stripos($ctype, 'application/json') !== false) {
            if (!$onlyBody) {
                return (@json_decode($raw, true) ?: []) + $this->request->get;
            } else {
                return @json_decode($raw, true);
            }
        } else {
            if (!$onlyBody) {
                parse_str($raw, $put);
                return ($put ?? []) + $this->request->get;
            } else {
                return $put ?? [];
            }
        }

    }

    protected function callMethodIfNot($httpmethod, $callable)
    {
        if ($this->request->server['REQUEST_METHOD'] === strtoupper($httpmethod)) {
            $callable();
            return true;
        }
        return false;
    }


    protected function callMethodIf($httpmethod, $callable)
    {
        if ($this->request->server['REQUEST_METHOD'] === strtoupper($httpmethod)) {
            $callable();
            return true;
        }
        return false;
    }


    protected function validateToken()
    {

        $data = $this->getData();

        if (!isset($data['user_token'])
            || ($data['user_token'] !== $this->session->data['user_token'])) {
            $this->sendJson(null, 401);

        }
        return true;
    }

    protected function validateMethod($method)
    {

        if (!is_array($method))
            $method = [$method];
        foreach ($method as $m)
            if ($this->request->server['REQUEST_METHOD'] === strtoupper($m)) {
                return true;
            }
        $this->sendJson(null, 405);
        return false;
    }


    protected function sendJson($data, $status = 200)
    {
        if ($data instanceof WP_Error) {
            $this->response->addHeader("HTTP/1.1 400 Bad Request");
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode([
                "data" => [
                    "code" => "element.error.dataerror.validation",
                    "errors" => $data->errors,
                    "errors_data" => $data->error_data
                ]
            ]));
            http_response_code($status);
            $this->response->output();
            exit;
        }

        if ($data !== null) {
            if (is_object($data)) {
                $this->load->model("extension/pplcz/normalizer");
                $data = $this->model_extension_pplcz_normalizer->normalize($data);
            } else if (is_array($data)) {
                $this->load->model("extension/pplcz/normalizer");
                array_walk_recursive($data, function (&$item) {
                    if (is_object($item)) {
                        $item = $this->model_extension_pplcz_normalizer->normalize($item);
                    }
                });
            }
            $this->response->setOutput(json_encode($data));
        } else {
            $this->response->setOutput("");
        }

        $message = "";
        switch ($status) {
            case 200:
                $message = '200 OK';
                break;
            case 201:
                $message = '201 Created';
                break;
            case 204:
                $message = '204 No Content';
                break;
            case 400:
                $message = '400 Bad Request';
                break;
            case 401:
                $message = '401 Unauthorized';
                break;
            case 403:
                $message = '403 Forbidden';
                break;
            case 404:
                $message = '404 Not Found';
                break;
            case 405:
                $message = '405 Method Not Allowed';
                break;
            case 500:
                $message = '500 Internal Server Error';
                break;
            default:
                $message = $status;
        }

        /**
         * @var Response $response
         */
        $response = $this->response;
        $response->addHeader("HTTP/1.1 $message");
        http_response_code($status);
        if ($data !== null)
            $response->addHeader('Content-Type: application/json');
        $response->output();
        exit;
    }
}