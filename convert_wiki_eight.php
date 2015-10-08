<?php
function get_nth($string, $index) {
                $char_val = substr($string, $index - 1, 1);
                if ($char_val == '\'') {
                    return '\'';
                }
            }

            function process_player($player, $player_aka) {
                $ret_val;
                $search_val = strpos($player_aka, $player);

                if ($search_val == FALSE) {

                    $ret_val = $player;
                    return $ret_val . ", " . ",";
                } else {
                    $char_check = substr($player_aka, $search_val - 1, 1);

                    if ($char_check == '\'') {
                        $ret_val = $player;

                        $flag_search_val = strpos($player_aka, "{{", $search_val);
                        $flag = substr($player_aka, $flag_search_val + 7);
                        $flag = substr($flag, 0, 2);

                        $ret_val = $ret_val . ',' . $flag;
                    } else {
                        $reverse_player_aka = strrev($player_aka);
                        $player_aka_len = strlen($player_aka);
                        $name_search_start = $player_aka_len - $search_val;
                        $name_search_val = strpos($reverse_player_aka, "'''", $name_search_start);
                        $name_reverse = substr($reverse_player_aka, $name_search_val + 3, 30);
                        $name_search_val = strpos($name_reverse, "'''");
                        $name_reverse = substr($name_reverse, 0, $name_search_val);
                        $player = strrev($name_reverse);

                        $ret_val = $player;

                        $search_val = strpos($player_aka, $player);
                        $flag_search_val = strpos($player_aka, "{{", $search_val);
                        $flag = substr($player_aka, $flag_search_val + 7);
                        $flag = substr($flag, 0, 2);

                        $ret_val = $ret_val . ',' . $flag;
                    }
                }
                $player = str_replace(' ', '_', $player);
                $player_info = file_get_contents('http://wiki.teamliquid.net/smash/api.php?action=query&titles='
                        . $player . '&prop=revisions&rvprop=content&format=json');
                $player_check = strpos($player_info, 'missing');
                if ($player_check == FALSE) {
                    $main_index_val = strpos($player_info, 'main=');
                    $main = substr($player_info, $main_index_val + 5);
                    $main_length = strpos($main, '\\n');
                    $main = substr($main, 0, $main_length);
                    $ret_val = $ret_val . ',' . $main;
                    $team_check = strpos($player_info, "team=\\n");
                    if ($team_check == FALSE) {
                        $team_index_val = strpos($player_info, 'team=');
                        $team = substr($player_info, $team_index_val + 5);
                        $team_length = strpos($team, '\\n');
                        $team = substr($team, 0, $team_length);
                        $team = strtolower($team);
                        $ret_val = $ret_val . ',' . $team;
                    }
                } else {
                    $ret_val = $ret_val . ", ";
                    return $ret_val;
                }

                return $ret_val;
            }

            function get_index($player_processed) {
                $end_index = strpos($player_processed, ',');
                return $end_index + 2;
            }

            function get_index_flag($player_processed) {
                $ret_val = get_index($player_processed);
                return $ret_val + 2;
            }

            function get_head($player_processed) {
                $ret_val = get_index_flag($player_processed);
                $index = strpos($player_processed, ',');
                $index_2 = strpos($player_processed, ',', $index + 1);
                $index_3 = strpos($player_processed, ',', $index_2 + 1);
                $index_rev = $index_3 - strlen($player_processed);
                $ret_val = substr($player_processed, $index_2 + 1, $index_rev);
                if (strlen($ret_val) > 1) {
                    return $ret_val;
                } else {
                    $start_index = get_index_flag($player_processed);
                    $ret_val = substr($player_processed, $start_index);
                    return $ret_val;
                }
            }

            function get_team($player_processed) {
                if (substr_count($player_processed, ",") != 3) {
                    return " ";
                }
                $index = strpos($player_processed, ',');
                $index_2 = strpos($player_processed, ',', $index + 1);
                $index_3 = strpos($player_processed, ',', $index_2 + 1);
                $index_rev = $index_3 - strlen($player_processed);
                $ret_val = substr($player_processed, $index_rev + 1);
                return $ret_val;
            }

            function get_player($player_processed) {
                $end_index = strpos($player_processed, ',');
                $ret_val = substr($player_processed, 0, $end_index);
                return $ret_val;
            }

            function get_flag($player_processed) {
                $start_index = get_index($player_processed);
                $end_index = strpos($player_processed, ',', $start_index);
                $ret_val = substr($player_processed, 0, $end_index);
                $ret_val = substr($ret_val, $start_index - 1);
                return $ret_val;
            }

            $player_aka = file_get_contents('http://wiki.teamliquid.net/smash/api.php?action=query&titles=Liquipedia:Players_AKA&export&format=json');
            $first_player = $_POST["first"];
            $second_player = $_POST["second"];
            $third_player = $_POST["third"];
            $fourth_player = $_POST["fourth"];
            $fifth_player = $_POST["fifth_1"];
            $sixth_player = $_POST["fifth_2"];
            $seventh_player = $_POST["seventh_1"];
            $eigth_player = $_POST["seventh_2"];
            $first_process = process_player($first_player, $player_aka);
            $second_process = process_player($second_player, $player_aka);
            $third_process = process_player($third_player, $player_aka);
            $fourth_process = process_player($fourth_player, $player_aka);
            $fifth_process = process_player($fifth_player, $player_aka);
            $sixth_process = process_player($sixth_player, $player_aka);
            $seventh_process = process_player($seventh_player, $player_aka);
            $eigth_process = process_player($eigth_player, $player_aka);

            echo "{{prize pool start|localcurrency=gbp|points=pcnt|maxheads=1}}
{{prize pool slot|place=1|usdprize=TBA|localprize=TBA|points=TBA|" . get_player($first_process) . " |flag1=" . get_flag($first_process) . "|heads1=" . get_head($first_process) . "|team1=" . get_team($first_process) . "}}
{{prize pool slot|place=2|usdprize=TBA|localprize=TBA|points=TBA|" . get_player($second_process) . " |flag1=" . get_flag($second_process) . "|heads1=" . get_head($second_process) . "|team1=" . get_team($second_process) . "}}
{{prize pool slot|place=3|usdprize=TBA|localprize=TBA|points=TBA|" . get_player($third_process) . " |flag1=" . get_flag($third_process) . "|heads1=" . get_head($third_process) . "|team1=" . get_team($third_process) . "}}
{{prize pool slot|place=4|usdprize=TBA|localprize=TBA|points=TBA|" . get_player($fourth_process) . " |flag1=" . get_flag($fourth_process) . "|heads1=" . get_head($fourth_process) . "|team1=" . get_team($fourth_process) . "}}
{{prize pool slot|place=5-6|usdprize=TBA|localprize=TBA|points=TBA
|" . get_player($fifth_process) . " |flag1=" . get_flag($fifth_process) . "|heads1=" . get_head($fifth_process) . "|team1=" . get_team($fifth_process) . "
|" . get_player($sixth_process) . " |flag2=" . get_flag($sixth_process) . "|heads2=" . get_head($sixth_process) . "|team2=" . get_team($sixth_process) . "
}}
{{prize pool slot|place=7-8|usdprize=TBA|localprize=TBA|points=TBA
|" . get_player($seventh_process) . " |flag1=" . get_flag($seventh_process) . "|heads1=" . get_head($seventh_process) . "|team1=" . get_team($seventh_process) . "
|" . get_player($eigth_process) . " |flag2=" . get_flag($eigth_process) . "|heads2=" . get_head($eigth_process) . "|team2=" . get_team($eigth_process) . "
}}
{{Prize pool end}}"
?>
