<?php
	// Tags
	const offenceTag = "offence";
	const defenceTag = "defence";
	const stealthTag = "stealth";
	const mixedTag = "mixed";

	// Magic Numbers
	const bonusPercentageToApply = 0.1;

	// Abbreviations
	const offenceAbbreviation = "OFF";
	const defenceAbbreviation = "DEF";
	const stealthAbbreviation= "STL";

	// Colours
	const offenceColour = "#ee5f5b";
	const defenceColour = "#5bc0de";
	const stealthColour = "#62c462";
	const mixedColour = "#ffd633";
	
	// Icons
	const offenceIcon = "fa-radiation";
	const defenceIcon = "fa-user-shield";
	const stealthIcon = "fa-user-secret";

	// Display encoding boundaries
	const minimumBaseStatPercentage = 0.05;	// Minimum of 5%
	const maximumBaseStatPercentage = 0.75;	// Maximum of 75%

	/* Calculate the stats given the initial ratios and bonus to apply */
	function calculateStats($totalStats, $offenceRatio, $defenceRatio, $stealthRatio, $bonus) {
		$offence = floor($totalStats * ($offenceRatio * 0.01));
		$defence = floor($totalStats * ($defenceRatio * 0.01));
		$stealth = floor($totalStats * ($stealthRatio * 0.01));

		// Grab the stats as calculated and run some wizadry to make sure the base
		// crime stats are correct to the original given total stats
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

		// Now calculate the bonus and apply it to the stats to be awarded
		switch ($bonus) {
			case offenceTag: {
				$offence = floor($offence + ($offence + bonusPercentageToApply));
			} break;

			case defenceTag: { 
				$defence += floor($defence + ($defence + bonusPercentageToApply));
			} break;
				
			case stealthTag: { 
				$stealth += floor($stealth + ($stealth + bonusPercentageToApply));
			} break;
				
			default: break;
		}

		// Add up the offence, defence and stealth to get the adjusted total stats after applying the bonus
		$adjustedTotalStats = ($offence + $defence + $stealth);
		
		// Return these stats
		return [$offence, $defence, $stealth, $adjustedTotalStats];
	}

	/* Calculate the stat distribution given the input crime */
	function calculateStatDistribution($crime) {
		// Grab the stats ratios and then figure out the stat distriubition
		$totalStats = $crime->C_totalStats;
		$offRatio = $crime->C_offenceRatio;
		$defRatio = $crime->C_defenceRatio;
		$stlRatio = $crime->C_stealthRatio;
		$bonus = $crime->C_bonus;

		// Grab the crime stats
		[$offence,
		 $defence,
		 $stealth,
		 $adjustedTotalStats] = calculateStats($totalStats, $offRatio, $defRatio, $stlRatio, $bonus);

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
			$firstDistributionStat = mixedTag;
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
						$firstDistributionStat = offenceTag;

						if ($offence > 0) {
							$secondDistributionStat = defenceTag;
						}
					} 
					break;

				case 1: 
					{ 
						$firstDistributionStat = offenceTag;

						if ($defence > 0) {
							$secondDistributionStat = stealthTag;
						}
					} 
					break;

				case 2: 
					{ 
						$firstDistributionStat = defenceTag;

						if ($stealth > 0) {
							$secondDistributionStat = stealthTag;
						}
					} 
					break;

				default: break;
			}
		}
		
		$isMixed = ($firstDistributionStat == mixedTag);
		$isOffence = (($firstDistributionStat == offenceTag) && ($secondDistributionStat == ""));
		$isDefence = (($firstDistributionStat == defenceTag) && ($secondDistributionStat == ""));
		$isStealth = (($firstDistributionStat == stealthTag) && ($secondDistributionStat == ""));
		$isOffDef = (($firstDistributionStat == offenceTag) && ($secondDistributionStat == defenceTag));
		$isOffStl = (($firstDistributionStat == offenceTag) && ($secondDistributionStat == stealthTag));
		$isDefStl = (($firstDistributionStat == defenceTag) && ($secondDistributionStat == stealthTag));
		
		// Calculate the off/def/stl ratios
		$baseStatPercentageDifference = (maximumBaseStatPercentage - minimumBaseStatPercentage);

		// Calculate the true distribution spreads and cap these to a maximum of 90% per stat 
		// and minimum of 5% per stats, to make sure we can see all 3 stats in the bars at all times, 
		// even if it is 100% weighted into one crime (which it probably should never be)
		$offStatRatio = ($offence / $adjustedTotalStats);
		$offStatFactor = (minimumBaseStatPercentage + ($offStatRatio * $baseStatPercentageDifference));
		$offStatPercentage = floor($offStatFactor * 100);

		$defStatRatio = ($defence / $adjustedTotalStats);
		$defStatFactor = (minimumBaseStatPercentage + ($defStatRatio * $baseStatPercentageDifference));
		$defStatPercentage = floor($defStatFactor * 100);

		$stlStatRatio = ($stealth / $adjustedTotalStats);
		$stlStatFactor = (minimumBaseStatPercentage + ($stlStatRatio * $baseStatPercentageDifference));
		$stlStatPercentage = floor($stlStatFactor * 100);

		// Now that we have the true distributions, we want to factor this down to slide between 
		// 0% -> 80% (as the last 20% we will reserve for indicating the bonus applied)
		// We will then apply the final 20% as an indicator to what bonus is applied (if any)
		$statBarBonusPercentageDifference = (1 - maximumBaseStatPercentage);
		
		// Determine the bonus values to return
		$bonusAbbreviation = "";
		$bonusColour = "";
		$bonusIcon = "";
		$offStatBonusPercentage = 0;
		$defStatBonusPercentage = 0;
		$stlStatBonusPercentage = 0;

		switch ($bonus) {
			case offenceTag: {
				$bonusAbbreviation = offenceAbbreviation;
				$bonusColour = offenceColour; 
				$bonusIcon = getStatTypeIcon(offenceTag);

				// Take the stats being improved and take their proportionate stat percentage
				// and apply to that the bar width to get a factor relevant to the bar width against
				// the stats in question. Should lead to better bonus visual results
				$bonusFactor = (($offStatFactor - minimumBaseStatPercentage) / $baseStatPercentageDifference);
				$offStatBonusPercentage = floor(($statBarBonusPercentageDifference * $bonusFactor) * 100);			
			} break;

			case defenceTag: { 
				$bonusAbbreviation = defenceAbbreviation;
				$bonusColour = defenceColour; 
				$bonusIcon = getStatTypeIcon(defenceTag);

				// Take the stats being improved and take their proportionate stat percentage
				// and apply to that the bar width to get a factor relevant to the bar width against
				// the stats in question. Should lead to better bonus visual results
				$bonusFactor = (($defStatFactor - minimumBaseStatPercentage) / $baseStatPercentageDifference);
				$defStatBonusPercentage = floor(($statBarBonusPercentageDifference * $bonusFactor) * 100);		
			} break;
				
			case stealthTag: { 
				$bonusAbbreviation = stealthAbbreviation;
				$bonusColour = stealthColour; 
				$bonusIcon = getStatTypeIcon(stealthTag);

				// Take the stats being improved and take their proportionate stat percentage
				// and apply to that the bar width to get a factor relevant to the bar width against
				// the stats in question. Should lead to better bonus visual results
				$bonusFactor = (($stlStatFactor - minimumBaseStatPercentage) / $baseStatPercentageDifference);
				$stlStatBonusPercentage = floor(($statBarBonusPercentageDifference * $bonusFactor) * 100);		
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
				$offStatPercentage,
				$defStatPercentage,
				$stlStatPercentage,
				$bonusAbbreviation,
			    $bonusColour,
			    $bonusIcon,
				$offStatBonusPercentage,
				$defStatBonusPercentage,
				$stlStatBonusPercentage];
	}

	function clamp($current, $min, $max) {
		return max($min, min($max, $current));
	}

	function getStatTypeIcon($statType) {
		switch ($statType) {
			case offenceTag: { return offenceIcon; } break;
			case defenceTag: { return defenceIcon; } break;
			case stealthTag: { return stealthIcon;  } break;
			default: break;
		}
		return;
	}
?>

