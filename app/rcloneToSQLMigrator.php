<?php


class RcloneToSqlMigrator extends RouteController{

    //$this->db_access->bind_param('is', $id, $day);

    public function __construct() {
        parent::__construct();
    }


    public function migrate_drive_entries_to_db($request, $response, $args){

        $allPostVars = $request->getParsedBody();

        if(isset($allPostVars['key']) && $allPostVars['key'] == SECRET_KEY_FOR_MIGRATION) {
            $games = $this->get_games_from_rclone();
    
            foreach($games as $game){
                
                $size = explode(' ', trim($game))[0];
                $path = str_replace($size . " ", "", trim($game));
                $temp = explode('/', $game);
                $file_type = $temp[count($temp)-2];
                $game = $temp[count($temp)-1];
                $game_id = substr(explode('[', $game)[1], 0, strlen(explode('[', $game)[1])-1);
                $file_ending = explode('.', $game)[count(explode('.', $game))-1];
                if($this->is_game_in_db($game, $game_id, $file_type, $file_ending, $size) == 0){
                    $this->add_game_to_db($game, $game_id, $file_type, $file_ending, $path, $size);
                } else {
                    error_log('Game ' . $game . ' already in the database');
                }
            }
    
            $response->getBody()->write(json_encode($this->get_games_from_rclone()));
            return $response->withHeader('Content-type', 'application/json')->withStatus(200);
        } else {
            return $response->withStatus(403);
        }
    }

    public function add_game_to_db(string $game_name, string $game_id, string $file_type, string $file_ending, string $path, $file_size){
        $sql = "INSERT INTO game_list (game_name, game_id, file_type, file_ending, path, file_size) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_string = $sql;
        $this->db_access->prepare($stmt_string);
        $this->db_access->bind_param("ssssss", $game_name, $game_id, $file_type, $file_ending, $path, $file_size);
        return $this->db_access->execute();
    }

    public function get_games_from_rclone(){
        exec('rclone ls '. DRIVE_NAME . ': --config=' . CONFIG_FILE_PATH_RCLONE, $output);
        return $output;
    }

    public function is_game_in_db(string $game_name, string $game_id, string $file_type, string $file_ending, string $file_size){
        $sql = "SELECT * FROM game_list where game_name = ? AND game_id = ? AND file_type = ? AND file_ending = ? AND file_size = ?;";
        $stmt_string = $sql;
        $this->db_access->prepare($stmt_string);
        $this->db_access->bind_param("sssss", $game_name, $game_id, $file_type, $file_ending, $file_size);
        $query_result = $this->format_query_result($this->db_access->execute());
        return count($query_result);
    }

    public function test($request, $response, $args){
        /*
        $sql = "SELECT * FROM game_list;";
        $stmt_string = $sql;
        $this->db_access->prepare($stmt_string);
        $query_result = $this->format_query_result($this->db_access->execute());
        */
        $string = '{
            "files": [
                "http://domain/file/1#Captain Tsubasa Rise of New Champions [Captain Tsubasa RoNC New Champions Uniform Set][0100CCA00DAE7005][GB][v0].nsz",
                "http://domain/file/2#Sid Meiers Civilization VI [Sid Meiers Civilization VI - Portugal Pack][010044500C18300A][US][v589824].nsz",
                "http://domain/file/3#Sid Meiers Civilization VI [Sid Meiers Civilization VI - Ethiopia Pack][010044500C183009][US][v655360].nsz",
                "http://domain/file/4#Sid Meies Civilization VI [Civilization VI Vietnam & Kublai Khan Pack][010044500C18300B][US][v655360].nsz",
                "http://domain/file/5#Sid Meierâs Civilization VI [Civilization VI Expansion Pack][010044500C183003][US][v720896].nsz",
                "http://domain/file/6#Sid Meiers Civilization VI [Civilization VI - Maya & Gran Colombia Pack][010044500C183004][US][v655360].nsz",
                "http://domain/file/7#Sid Meie Civilization VI [Sid Meiers Civilization VI - Babylon Pack][010044500C183006][US][v655360].nsz",
                "http://domain/file/8#FUSER [Panic! At The Disco - Dancings Not A Crime][0100E1F013675066][PE][v0].nsz"
            ],
            "success": "dis work lel"
        }';
        $response->getBody()->write($string);
        return $response->withHeader('Content-type', 'application/json');
    }
}



?>