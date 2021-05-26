<?php
use Psr\Http\Message\StreamFactoryInterface;
use Slim\Psr7\Factory\StreamFactory;

class RcloneInterface extends RouteController{
    

    //$this->db_access->bind_param('is', $id, $day);
    private $streamFactory;

    public function __construct(StreamFactoryInterface $streamFactory) {
        $this->streamFactory = $streamFactory;
        parent::__construct();
    }


   

    public function get_games_from_rclone(){
        exec('rclone ls '. DRIVE_NAME . ': --config=' . CONFIG_FILE_PATH_RCLONE, $output);
        return $output;
    }

    public function is_game_in_db(string $game_name, string $game_id, string $file_type, string $file_ending){
        $sql = "SELECT * FROM game_list where game_name = ? AND game_id = ? AND file_type = ? AND file_ending = ?;";
        $stmt_string = $sql;
        $this->db_access->prepare($stmt_string);
        $this->db_access->bind_param("ssss", $game_name, $game_id, $file_type, $file_ending);
        $query_result = $this->format_query_result($this->db_access->execute());
        return count($query_result);
    }

    public function get_all_game_base($request, $response, $args){
        $sql = "SELECT game_name FROM game_list WHERE file_type = 'base'";
        $stmt_string = $sql;
        $this->db_access->prepare($stmt_string);
        $query_result = $this->format_query_result($this->db_access->execute());

        $response->getBody()->write(json_encode($query_result));
        return $response->withHeader('Content-type', 'application/json');
    }

    public function get_all_game_dlc($request, $response, $args) {
        $sql = "SELECT game_name FROM game_list WHERE file_type = 'dlc'";
        $stmt_string = $sql;
        $this->db_access->prepare($stmt_string);
        $query_result = $this->format_query_result($this->db_access->execute());

        $response->getBody()->write(json_encode($query_result));
        return $response->withHeader('Content-type', 'application/json');
    }

    public function get_all_game_updates($request, $response, $args) {
        $sql = "SELECT game_name FROM game_list WHERE file_type = 'updates'";
        $stmt_string = $sql;
        $this->db_access->prepare($stmt_string);
        $query_result = $this->format_query_result($this->db_access->execute());

        $response->getBody()->write(json_encode($query_result));
        return $response->withHeader('Content-type', 'application/json');
    }

    public function get_all_entries($request, $response, $args) {
        $sql = "SELECT * FROM game_list;";
        $stmt_string = $sql;
        $this->db_access->prepare($stmt_string);
        $query_result = $this->format_query_result($this->db_access->execute());
        $links = array();
        $links['files'] = array();
        $links['success'] = SUCCESS_MESSAGE;
        foreach($query_result as $row){
            array_push($links['files'], DOMAIN_NAME . '/files/' . $row['id'] . '#'.$row['game_name']);
        }
        $response->getBody()->write(json_encode($links, JSON_UNESCAPED_SLASHES));
        return $response->withHeader('Content-type', 'application/json');
    }

    public function get_game_base($request, $response, $args) {
        #$response->getBody()->write();
        $gamename = $this->get_game_by_id($args['id']);
        $file = $this->get_game_from_drive($args['id']);
        if(!$gamename || !$file ){
            return $response->withStatus(400);
        }
        $file_stream = $this->streamFactory->createStreamFromFile(FILE_CACHE_PATH . $gamename);
        return $response->withBody($file_stream)
        ->withHeader('Content-Type', 'application/octet-stream')
        ->withHeader('Content-Length', filesize(FILE_CACHE_PATH . $gamename))
        ->withHeader('Content-Disposition', 'filename='.$gamename.';');
    }

    public function is_file_in_cache(string $file) {

    }
    /**
     * check if game is already in cache,
     * if not check if enough space is available
     * if not remove files until enough space is available
     * than download the file
     */
    public function get_game_from_drive($id) {
        $gamename = $this->get_game_by_id($id);
        if(!$gamename){
            return false;
        }
        $gamepath = $this->get_game_path_by_id($id);
        if(!$gamepath){
            return false;
        }
        error_log('used space: ' . $this->get_directory_size(FILE_CACHE_PATH));
        if(in_array($gamename, scandir(FILE_CACHE_PATH))){
            error_log('File is in cache, serving local copy...');
            return fopen(FILE_CACHE_PATH . $gamename, "r");
        }else if($this->get_directory_size(FILE_CACHE_PATH) < (ROUGH_SPACE_LIMIT*1024*1024)) {
            error_log('couldnt find file in cache, downloading from remote...');
            exec('rclone copy '. DRIVE_NAME . ':"' . $gamepath . '" ' . FILE_CACHE_PATH . " --config=" . CONFIG_FILE_PATH_RCLONE);
            return fopen(FILE_CACHE_PATH . $gamename, "r");
        } else {
            while($this->get_directory_size(FILE_CACHE_PATH) > (ROUGH_SPACE_LIMIT*1024*1024)){
                error_log('need to make room...');
                $array = scandir(FILE_CACHE_PATH);
                exec('rm ' . '"'.FILE_CACHE_PATH.$array[count($array) - 1].'"');
                break;
            }
            exec('rclone copy '. DRIVE_NAME . ':"' . $gamepath . '" ' . FILE_CACHE_PATH . " --config=" . CONFIG_FILE_PATH_RCLONE);
            return fopen(FILE_CACHE_PATH . $gamename, "r");
        }
    }

    function get_directory_size($path) {
        $bytestotal = 0;
        $path = realpath($path);
        if($path!==false && $path!='' && file_exists($path)){
            foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
                $bytestotal += $object->getSize();
            }
        }
        return $bytestotal;
    }

    public function get_game_by_id(int $id) {
        $sql = "SELECT game_name FROM game_list where id = ?;";
        $stmt_string = $sql;
        $this->db_access->prepare($stmt_string);
        $this->db_access->bind_param("i", $id);
        $query_result = $this->format_query_result($this->db_access->execute());
        $query_result = $this->assoc_array_to_indexed($query_result);
        if(count($query_result) > 0){
            return $query_result[0]['game_name'];
        } else {
            return false;
        }
        
    }

    public function get_game_path_by_id(int $id) {
        $sql = "SELECT path FROM game_list where id = ?;";
        $stmt_string = $sql;
        $this->db_access->prepare($stmt_string);
        $this->db_access->bind_param("i", $id);
        $query_result = $this->format_query_result($this->db_access->execute());
        $query_result = $this->assoc_array_to_indexed($query_result);
        if(count($query_result) > 0){
            return $query_result[0]['path'];
        } else {
            return false;
        }
    }
}



?>