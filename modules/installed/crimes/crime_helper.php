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
	const mixedAbbreviation= "MIX";

	// Colours
	const offenceColour = "#ee5f5b";
	const defenceColour = "#5bc0de";
	const stealthColour = "#62c462";
	const mixedColour = "#ffd633";
	
	// Icons
	const offenceIcon = "fa-radiation";
	const defenceIcon = "fa-user-shield";
	const stealthIcon = "fa-user-secret";
	const mixedIcon = "fa-balance-scale";

	// Display encoding boundaries
	const minimumBaseStatPercentage = 0.05;	// Minimum of 5%
	const maximumBaseStatPercentage = 0.75;	// Maximum of 75%

	// Stat Indicator Factors
	const mixedMaxStatDifference = 5;
	const fullStatPercentage = 60;

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
		return [$offence, 
				$defence, 
				$stealth, 
				$adjustedTotalStats];
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

		// Add the adjusted stat percentage + bonus percentage together to get the new true stat percentage
		$adjustedOffenceRatio = ($offStatPercentage + $offStatBonusPercentage);
		$adjustedDefenceRatio = ($defStatPercentage + $defStatBonusPercentage);
		$adjustedStealthRatio = ($stlStatPercentage + $stlStatBonusPercentage);

		// first lets see if it's a full mixed crime by determining the max difference
		// between the 3 crime stat categories
		$offDefDifference = abs($adjustedOffenceRatio - $adjustedDefenceRatio);
		$offStlDifference = abs($adjustedOffenceRatio - $adjustedStealthRatio);
		$defStlDifference = abs($adjustedDefenceRatio - $adjustedStealthRatio);
		$maxDifference = max($offDefDifference, $offStlDifference, $defStlDifference);
		
		$distributionStat1 = "";
		$distributionStat1Colour = "";
		$distributionStat1Icon = "";
		$distributionStat2 = "";
		$distributionStat2Colour = "";
		$distributionStat2Icon = "";

		// If the max difference between the 3 stats is less than or equal to 5
		// we determine it as a mixed crime
		if ($maxDifference <= mixedMaxStatDifference) {
			// We determine this is a full mixed crime
			$distributionStat1 = mixedTag;
			$distributionStat1Colour = mixedColour;
			$distributionStat1Icon = mixedIcon;
			$distributionStat2 = "";
		} else
		{
			// We now want to determine if this is a full stat crime
			$maxStatRatio = max($adjustedOffenceRatio, $adjustedDefenceRatio, $adjustedStealthRatio);
			if ($maxStatRatio >= fullStatPercentage) {
				// This is a full stat so grab which one it is

				// Create an array to easily detect the min/max of the stats as needed
				$statArray = array(
					0 => $adjustedOffenceRatio, 
					1 => $adjustedDefenceRatio, 
					2 => $adjustedStealthRatio
				);
				$index = array_search(max($statArray), $statArray);

				switch ($index) {
					case 0: 
						{ 
							$distributionStat1 = offenceTag;
							$distributionStat1Colour = offenceColour;
							$distributionStat1Icon = offenceIcon;
						} 
						break;

					case 1: 
						{ 
							$distributionStat1 = defenceTag;
							$distributionStat1Colour = defenceColour;
							$distributionStat1Icon = defenceIcon;
						} 
						break;

					case 2: 
						{ 
							$distributionStat1 = stealthTag;
							$distributionStat1Colour = stealthColour;
							$distributionStat1Icon = stealthIcon;
						} 
						break;

					default: break;
				}
			} else {
				
				// This is not a mixed crime so now we want to determine it's
				// mix basis (off/def, off/stl, def/stl)
				$offDefTotal = ($adjustedOffenceRatio + $adjustedDefenceRatio);
				$offStlTotal = ($adjustedOffenceRatio + $adjustedStealthRatio);
				$defStlTotal = ($adjustedDefenceRatio + $adjustedStealthRatio);

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
							$distributionStat1 = offenceTag;
							$distributionStat1Colour = offenceColour;
							$distributionStat1Icon = offenceIcon;

							if ($offence > 0) {
								$distributionStat2 = defenceTag;
								$distributionStat2Colour = defenceColour;
								$distributionStat2Icon = defenceIcon;
							}
						} 
						break;

					case 1: 
						{ 
							$distributionStat1 = offenceTag;
							$distributionStat1Colour = offenceColour;
							$distributionStat1Icon = offenceIcon;

							if ($defence > 0) {
								$distributionStat2 = stealthTag;
								$distributionStat2Colour = stealthColour;
								$distributionStat2Icon = stealthIcon;
							}
						} 
						break;

					case 2: 
						{ 
							$distributionStat1 = defenceTag;
							$distributionStat1Colour = defenceColour;
							$distributionStat1Icon = defenceIcon;

							if ($stealth > 0) {
								$distributionStat2 = stealthTag;
								$distributionStat1Colour = stealthColour;
								$distributionStat2Icon = stealthIcon;
							}
						} 
						break;

					default: break;
				}
			}
		}
		
		// Grab the stat leaning indicators
		$isMixed = ($distributionStat1 == mixedTag);
		$isOffence = (($distributionStat1 == offenceTag) && ($distributionStat2 == ""));
		$isDefence = (($distributionStat1 == defenceTag) && ($distributionStat2 == ""));
		$isStealth = (($distributionStat1 == stealthTag) && ($distributionStat2 == ""));
		$isOffDef = (($distributionStat1 == offenceTag) && ($distributionStat2 == defenceTag));
		$isOffStl = (($distributionStat1 == offenceTag) && ($distributionStat2 == stealthTag));
		$isDefStl = (($distributionStat1 == defenceTag) && ($distributionStat2 == stealthTag));

		// Return crime info
		return [$distributionStat1, 
				$distributionStat2,
				$distributionStat1Colour,
				$distributionStat2Colour,
				$distributionStat1Icon,
				$distributionStat2Icon,
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

