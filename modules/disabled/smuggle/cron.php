<?php

	require __DIR__ . '/../../../config.php';
	require __DIR__ . '/../../../dbconn.php';


		$stocks = $db->selectAll("SELECT * FROM stocks");

		foreach ($stocks as $stock) {

			$min = $stock["ST_min"];
			$max = $stock["ST_max"];
			$volitile = $stock["ST_vol"] / 1000;
			$rising = $stock["ST_rising"];
            $history = explode(",", $stock["ST_history"]);
            $stockCost = $history[count($history) - 1];
            $nextChange = $stock["ST_change"];

			$maxChange = $stockCost * $volitile;

            if ($rising) {
                $stockCost = mt_rand(floor($stockCost - $maxChange * 0.2), ceil($stockCost + $maxChange * 0.8));
            } else {
                $stockCost = mt_rand(floor($stockCost - $maxChange * 0.8), ceil($stockCost + $maxChange * 0.2));
            }

            if ($stockCost < $min) {
                $stockCost = $min;
                $rising = true;
                $nextChange = 1;
            }

            if ($stockCost > $max) {
                $stockCost = $max;
                $rising = false;
                $nextChange = 1;
            }

            if ($nextChange == 1) {
                $nextChange = mt_rand(10, 30);
                $rising = !!mt_rand(0, 1);
            }

            if (count($history) == 60) {
            	array_shift($history);
            }

            $history[] = $stockCost;

            $db->update("
            	UPDATE stocks SET ST_history = :h, ST_change = :c, ST_rising = :r WHERE ST_id = :id
            ", array(
            	":h" => implode(",", $history), 
            	":c" => $nextChange, 
            	":r" => intval($rising), 
            	":id" =>$stock["ST_id"]
            ));

		}


?>
