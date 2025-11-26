<?php
namespace PPLCZ\Repository;


use Opencart\Admin\Model\Extension\Pplcz\Normalizer;
use Opencart\Admin\Model\Localisation\Country;
use Opencart\Admin\Model\Setting\Setting;
use Opencart\Admin\Model\Setting\Store;
use Opencart\System\Engine\Model;
use PPLCZ\Data\LogData;
use PPLCZ\Model\Model\CollectionAddressModel;
use PPLCZ\Model\Model\CountryModel;
use PPLCZ\Model\Model\ShipmentMethodModel;
use PPLCZ\Model\Model\ShopModel;
use Throwable;

class Log extends Data
{


    private function format_args($args) {
        $formatted = [];
        foreach ($args as $arg) {
            if (is_object($arg)) {
                $formatted[] = get_class($arg);
            } elseif (is_array($arg)) {
                $formatted[] = 'Array(' . count($arg) . ')';
            } elseif (is_resource($arg)) {
                $formatted[] = 'Resource';
            } elseif (is_null($arg)) {
                $formatted[] = 'NULL';
            } elseif (is_bool($arg)) {
                $formatted[] = $arg ? 'true' : 'false';
            } elseif (is_string($arg)) {
                $formatted[] = "'" . (mb_strlen($arg) > 70 ? mb_substr($arg, 0, 70) . '...' : $arg) . "'";
            } else {
                $formatted[] = (string) $arg;
            }
        }
        return join(', ', $formatted);
    }

    public function shutdown_handler($ex = null)
    {
        static $resolve;
        if ($resolve)
            return;
        $resolve = true;



        $error = error_get_last();
        if ($error || $ex)
        {
            if ($ex)
            {
                $message = explode("\n", $ex->getMessage() . '('. get_class($ex) . ')'
                    . " in " . $ex->getFile() . ":" . $ex->getLine()
                    . "\nTrace:\n" . $ex->getTraceAsString());
                $path = realpath(__DIR__ . '/../../');
            }
            else {
                $message = explode("\n", $error['message']);
                $path = realpath(__DIR__ . '/../../');
            }
            if (array_filter($message, function($item) use($path){
                return strpos($item, $path) !== false;
            }))
            {
                $logsetting = $this->get_log_setting();
                $max = $logsetting['max'];
                $message = join("\n", $message);
                $hashes = $logsetting['hashes'];
                $hash = sha1($message);

                if (intval($max) < 100  && strpos("$hashes" ?: '', $hash) === false) {
                    try {
                        $logdata = new \PPLCZ\Data\LogData($this->registry);
                        $logdata->message = $message;
                        $logdata->errorhash = $hash;
                        $logdata->timestamp = (date('Y-m-d H:i:s'));
                        $this->save($logdata);
                    } catch (\Throwable $ex) {

                    }
                }
            }
        }

        $resolve = false;
    }

    private $previous;

    public function error_handler($errno, $errstr, $errfile, $errline) {
        static $resolve;
        if ($resolve)
            return;
        $resolve = true;

        $backtrace = debug_backtrace();
        $path = realpath(__DIR__ . '/../..');
        $inplugin = strpos($errfile, $path) !== false;
        $out= [
            $errstr,
            "Stack trace:",

        ];

        foreach ($backtrace as $key => $frame)
        {
            $file = "emptyfile";
            if (isset($frame['file']))
                $file = $frame['file'];

            $inplugin = $inplugin || strpos($file, $path) !== false;
            $file = isset($frame['file']) ? $frame['file'] : '[internal function]';
            $line = isset($frame['line']) ? $frame['line'] : '';
            $function = isset($frame['function']) ? $frame['function'] : '';
            $args = isset($frame['args']) ? $this->format_args($frame['args']) : '';
            $out[] = "#$key $file($line): $function($args)";
        }

        if ($inplugin)
        {
            $logsetting = $this->get_log_setting();
            $max = $logsetting['max'];
            $hashes = $logsetting['hashes'];
            $error = $errstr . "\n" . join("\n", $out);
            $hash = sha1($error);
            if (intval($max) < 100 && strpos("$hashes" ?: '', $hash) === false) {
                try {
                    $logdata = new LogData($this->registry);
                    $logdata->message = ($error);
                    $logdata->errorhash = ($hash);
                    $logdata->timestamp = (date('Y-m-d H:i:s'));
                    $this->save($logdata);
                }
                catch (\Throwable $ex) {

                }
            }
        }
        $resolve = false;
    }

    private function get_log_setting()
    {
        $prefix = DB_PREFIX;
        $db = $this->db;

        $result = $db->query("SELECT * FROM {$prefix}setting where `key` in ('shipping_pplcz_error_log_hashes','shipping_pplcz_error_log')");
        $max = 0;
        $hashes = "";
        foreach ($result->rows as $row)
        {
            if ($row['key'] === 'shipping_pplcz_error_log_hashes')
                $hashes = $row['value'];
            else if ($row['key'] === 'shipping_pplcz_error_log' )
                $max = $row['value'];
        }
        return [
            'max' => $max,
            'hashes' => $hashes,
        ];
    }

    public function add_log_to_setting($addtotable, $hash)
    {
        $db = $this->db;
        $prefix = $db->prefix;
        $db->query("UPDATE {$prefix}setting SET `value` =
 CAST((
    CASE
        WHEN option_value REGEXP '^[0-9]+$'
        THEN CAST(option_value AS UNSIGNED) + 1
        ELSE 1
    END
) AS CHAR)
WHERE `key` = 'shipping_pplcz_error_log'
 ");
        $db->query("UPDATE {$prefix}setting
SET option_value = trim(concat(ifnull(`value`, ''), '\n', '$hash'))
WHERE `key` = 'shipping_pplcz_error_log_hashes' and `value` not like '%$hash%'");

    }

    public function exception_handler(\Throwable $ex)
    {
        $this->shutdown_handler($ex);

        if ($this->previous !== null) {
            call_user_func($this->previous, $ex);
        }
    }

    public function attach()
    {
        set_error_handler([$this, "error_handler"]);
        register_shutdown_function([$this, "shutdown_handler"]);
        $this->previous = set_exception_handler([$this, "exception_handler"]);


    }

}
