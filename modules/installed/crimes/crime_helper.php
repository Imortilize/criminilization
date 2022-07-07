<?php
	function calculateStats($totalStats, $offenceRatio, $defenceRatio, $stealthRatio) {
		$offence = floor($totalStats * ($offenceRatio * 0.01));
		$defence = floor($totalStats * ($defenceRatio * 0.01));
		$stealth = floor($totalStats * ($stealthRatio * 0.01));
		
		$currentStatsToAward = ($offence + $defence + $stealth);
		if ($currentStatsToAward != $totalStats) {

			// Grab the difference
			$difference = ($totalStats - $currentStatsToAward);

			// Create an array to easily detect the min/max of the stats as needed
			$statArray = array(
				0 => $offenceRatio, 
				1 => $defenceRatio, 
				2 => $stealthRatio
			);

			// Index to apply to
			$index = -1;

			// The current stats being awarded doesn't match the total stats
			// which means we have a math error due to ratios, so lets adjust
			// appropiately
			if ($currentStatsToAward < $totalStats) {
				// We have less stats so find the crime with the highest ratio and
				// add the remainder to it
				$index = array_search(max($statArray), $statArray);
			} else {
				// We have more stats so find the crime with the lowest ratio and
				// remove the remainder from it
				$index = array_search(min($statArray), $statArray);
			}

			switch ($index) {
				case 0: { $offence += $difference; } break;
				case 1: { $defence += $difference; } break;
				case 2: { $stealth += $difference; } break;
				default: break;
			}
		}
		
		return [$offence, $defence, $stealth];
	}

	function calculateStatDistribution($crime) {
		// Grab the stats ratios and then figure out the stat distriubition
		$totalStats = $crime->C_totalStats;
		$offRatio = $crime->C_offenceRatio;
		$defRatio = $crime->C_defenceRatio;
		$stlRatio = $crime->C_stealthRatio;
		$bonus = $crime->C_bonus;

		// first lets see if it's a full mixed crime by determining the max difference
		// between the 3 crime stat categories
		$offDefDifference = abs($offRatio - $defRatio);
		$offStlDifference = abs($offRatio - $stlRatio);
		$defStlDifference = abs($defRatio - $stlRatio);
		$maxDifference = max($offDefDifference, $offStlDifference, $defStlDifference);
		
		$firstDistributionStat = "";
		$secondDistributionStat = "";

		// If the max difference between the 3 stats is less than or equal to 5
		// we determine it as a mixed crime
		if ($maxDifference <= 5) {
			// We determine this is a full mixed crime
			$firstDistributionStat = "mixed";
			$secondDistributionStat = "";
		} else
		{
			// This is not a mixed crime so now we want to determine it's
			// mix basis (off/def, off/stl, def/stl)
			$offDefTotal = ($offRatio + $defRatio);
			$offStlTotal = ($offRatio + $stlRatio);
			$defStlTotal = ($defRatio + $stlRatio);

			// Create an array to easily detect the min/max of the stats as needed
			$statArray = array(
				0 => $offDefTotal, 
				1 => $offStlTotal, 
				2 => $defStlTotal
			);
			$index = array_search(max($statArray), $statArray);

			switch ($index) {
				case 0: 
					{ 
						$firstDistributionStat = "offence";

						if ($defence > 0) {
							$secondDistributionStat = "defence";
						}
					} 
					break;

				case 1: 
					{ 
						$firstDistributionStat = "offence";

						if ($stealth > 0) {
							$secondDistributionStat = "stealth";
						}
					} 
					break;

				case 2: 
					{ 
						$firstDistributionStat = "defence";

						if ($stealth > 0) {
							$secondDistributionStat = "stealth";
						}
					} 
					break;

				default: break;
			}
		}
		
		$offColour = "#ee5f5b";
		$defColour = "#5bc0de";
		$stlColour = "#62c462";
		$mixedColour = "#ffd633";
		
		$isMixed = ($firstDistributionStat == "mixed");
		$isOffence = (($firstDistributionStat == "offence") && ($secondDistributionStat == ""));
		$isDefence = (($firstDistributionStat == "defence") && ($secondDistributionStat == ""));
		$isStealth = (($firstDistributionStat == "stealth") && ($secondDistributionStat == ""));
		$isOffDef = (($firstDistributionStat == "offence") && ($secondDistributionStat == "defence"));
		$isOffStl = (($firstDistributionStat == "offence") && ($secondDistributionStat == "stealth"));
		$isDefStl = (($firstDistributionStat == "defence") && ($secondDistributionStat == "stealth"));
		
		// Grab the crime stats
		[$offence,
		 $defence,
		 $stealth] = calculateStats($totalStats, $offRatio, $defRatio, $stlRatio);
		
		// Calculate the true distribution spreads and cap these to a maximum of 90% per stat 
		// and minimum of 5% per stats, to make sure we can see all 3 stats in the bars at all times, 
		// even if it is 100% weighted into one crime (which it probably should never be)
		$OffStatPercentage = clamp((($offence > 0) ? ($offence / $totalStats) : 0), 0.05, 0.9);
		$DefStatPercentage = clamp((($defence > 0) ? ($defence / $totalStats) : 0), 0.05, 0.9);
		$StlStatPercentage = clamp((($stealth > 0) ? ($stealth / $totalStats) : 0), 0.05, 0.9);
		
		// Now that we have the true distributions, we want to factor this down to slide between 
		// 0% -> 80% (as the last 20% we will reserve for indicating the bonus applied)
		// We will then apply the final 20% as an indicator to what bonus is applied (if any)
		$statBarBasePercentage = 80;
		$statBarBonusPercentage = (100 - $statBarBasePercentage);
		
		$OffStatPercentage = floor($OffStatPercentage * $statBarBasePercentage);
		$DefStatPercentage = floor($DefStatPercentage * $statBarBasePercentage);
		$StlStatPercentage = floor($StlStatPercentage * $statBarBasePercentage);
		
		// Determine the bonus values to return
		$bonusAbbreviation = "";
		$bonusColour = "";
		$bonusIcon = "";
		$bonusRatio = 0;
		switch ($bonus) {
			case "offence": {
				$bonusAbbreviation = "OFF";
				$bonusColour = $offColour; 
				$bonusIcon = "fa-radiation";
			} break;
			case "defence": 
				{ 
					$bonusAbbreviation = "DEF";
					$bonusColour = $defColour; 
					$bonusIcon = "fa-user-shield";
				} break;
				
			case "stealth": 
				{ 
					$bonusAbbreviation = "STL";
					$bonusColour = $stlColour; 
					$bonusIcon = "fa-user-secret";
				} break;
				
			default: break;
		}

		// Return crime info
		return [$firstDistributionStat, 
				$secondDistributionStat,
				$isMixed, 
				$isOffence,
				$isDefence,
				$isStealth,
				$isOffDef, 
				$isOffStl, 
				$isDefStl,
				$OffStatPercentage,
				$DefStatPercentage,
				$StlStatPercentage,
			   	$offColour,
			    $defColour,
			   	$stlColour,
				$bonusAbbreviation,
			    $bonusColour,
			    $bonusIcon];
	}

	function clamp($current, $min, $max) {
		return max($min, min($max, $current));
	}
?>

