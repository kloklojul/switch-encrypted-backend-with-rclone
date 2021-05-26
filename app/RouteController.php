<?php

use Psr\Container\ContainerInterface;


class RouteController {

    protected $db_ops;
    protected $db_access;

    // constructor receives container instance
    public function __construct() {
        $this->db_access = DatabaseAccess::get_instance();
    }


   public static function encode_items_url(&$item, $key){
	$item = rawurlencode($item);
   }

   public static function encode_items(&$item, $key){
	  $item = utf8_encode($item);
	}

    protected function execute_stmt($stmt_string, $param_string, ...$args)
    {
        $no_error = $this->db_access->prepare($stmt_string);

        if ($no_error && sizeof($args) > 0) {
            $no_error = $this->db_access->bind_param($param_string, ...$args);
        }

        $query_result = null;
        if ($no_error) {
            $query_result = $this->format_query_result($this->db_access->execute());
        } else {
            return [];
        }
        if (!$query_result) {
            return [];
        }
        $result = [];
        foreach ($query_result as $row) {
            $result[] = $row;
        }

        return $result;
    }

    public function assoc_array_to_indexed($assoc_array)
    {
        $indexed_array = [];
        foreach ($assoc_array as $value) {
            $indexed_array[] = $value;
        }
        return $indexed_array;
    }

    protected function format_query_result($query_result)
    {
        $result = [];
        while ($row = $query_result->fetch_assoc()) {
            #App\Exceptions\HttpErrorHandler::add_errortext($counter);
            array_walk_recursive($row, [$this, 'encode_items']);
            array_push($result, $row);
        }
        return $result;
    }

    /**
     * Translates a db query result to an indexed array.
     * Works if only one field is selected.
     * @param $query_result
     * @param bool $value_is_int result array items will be converted to int
     * @return array
     */
    protected function format_query_result_to_indexed_array($query_result, $value_is_int = false) {
        $query_result = $this->format_query_result($query_result);
        $result = [];
        foreach ($query_result as $object) {
            foreach ($object as $key => $value) {
                if ($value_is_int)
                    $value = intval($value);
                array_push($result, $value);
            }
        }
        return $result;
    }
}