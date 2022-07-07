<?php	

	function getAward($id = null) 
	{
		global $db;
		$awards = $db->prepare("
			SELECT
				AW_id as 'id',  
				AW_img as 'img',
				AW_name as 'name',  
				AW_desc as 'desc',
				AW_type as 'type',
				AW_required as 'required',
				AW_money as 'money',
				AW_bullets as 'bullets',
				AW_points as 'points',
				AW_hidden as 'hidden'
			FROM awards
			" . ($id ? "
			WHERE AW_id = :id" : "") . "
			ORDER BY AW_name"
		);
		if ($id) {
			$awards->bindParam(':id', $id);
		}
		$awards->execute();
		if (!$id) {
			return $awards->fetchAll(PDO::FETCH_ASSOC);
		}
		return $awards->fetch(PDO::FETCH_ASSOC);
	}
		
	function getUserAward($id,$award)
	{
		global $db;
		$awards = $db->prepare ("
			SELECT 
				UA_user as 'user', 
				UA_award as 'award',
				UA_time as 'time'
			FROM
				userAwards
			WHERE 
				UA_user = :user
			" . ($award ? "
			AND UA_award = :award" : "") . "
			ORDER BY 
				UA_time 
			DESC");
		$awards->bindParam(":user", $id);
		if ($award) {
			$awards->bindParam(':award', $award);
		}
		$awards->execute();
		if (!$award) {
			return $awards->fetchAll(PDO::FETCH_ASSOC);
		}
		return $awards->rowCount();
	}
	
	new Hook("alterModuleTemplate", function ($template) {
		global $page, $user, $db;
		if ($template["templateName"] == 'profile') {
			$userAwards = array();
			if(isset($_GET['view'])){
				$id = $_GET['view'];
			} else {
				$id = $user->id;
			}
			$awards = getUserAward($id,null);
			foreach ($awards as $award) {
				$userAwards[] = array(
					"award" => getAward($award["award"]), 
					"time" => date('d M Y', $award["time"])
				);
			}
			$template["items"]["userAwards"] = $userAwards;
			$template["html"] = $template["html"] . '
			{#if userAwards}
				<div class="panel panel-default">
					<div class="panel-heading">Awards</div>
					<div class="panel-body">
						<div class="row">
						{#each userAwards}	
							<div class="col-md-2 col-xs-3">
								<img src="/modules/installed/awards/images/{award.img}" title="{#if award.hidden}Hidden Award{/if}{#unless award.hidden}<{award.desc}>{/unless} [Awarded {time}]" class="img-responsive">
							</div>
						{/each}
						</div>
					</div>
				</div>
			{/if}
			';
		}
		return $template;
	});

	new hook("accountMenu", function ($user) {
		global $db;
		if($user){
			$awards = getAward();
			foreach ($awards as $award) {		
				$type = "US_".preg_replace("/\s+/", "", strtolower($award['type']));
				if ($user->info->$type >= $award["required"]) {
					$check = getUserAward($user->info->US_id,$award["id"]);
					if($check == 0){
						$insert = $db->prepare("INSERT INTO `userAwards` (`UA_user`, `UA_award`, `UA_time`)
						VALUES (:user, :award, UNIX_TIMESTAMP());");
						$insert->bindParam(":user", $user->info->US_id);
						$insert->bindParam(":award", $award['id']);
						$insert->execute();
						$extra = "";
						if($award['money'] > 0){ 
							$user->set("US_money", $user->info->US_money + $award['money']);
							$extra .= "<li><strong>Cash:</strong> $" . number_format($award['money']) . "</li>";
						}
						if($award['bullets'] > 0){
							$user->set("US_bullets", $user->info->US_bullets + $award['bullets']);
							$extra .= "<li><strong>Bullets:</strong> " . number_format($award['bullets']) . "</li>";
						}
						if($award['points'] > 0){
							$user->set("US_points", $user->info->US_points + $award['points']);
							$extra .= "<li><strong>Points:</strong> " . number_format($award['points']) . "</li>";
						}
						$AwardNotification = 'You have just earned the '. $award['name'] . ' award.' . ($extra != '' ? '<br/>You have been rewarded with<br/><ul>' . $extra . '</ul>' : '');
						$user->newNotification($AwardNotification); 
					}
				}
			}
		}
        return array(
            "url" => "?page=awards", 
            "text" => "Awards"
        );
    });
	
	new Hook("userAction", function ($data) {
		global $db, $user;
		if($data['module'] == "crimes") {
			$user->set("US_crimesdone", $user->info->US_crimesdone + 1);
			if($data['success'] == "true"){
				$user->set("US_crimesuccess", $user->info->US_crimesuccess + 1);
			} else {
				$user->set("US_crimesfail", $user->info->US_crimesfail + 1);
			}
		}
		if($data['module'] == "theft") {
			$user->set("US_theftdone", $user->info->US_theftdone + 1);
			if($data['success'] == "true"){
				$user->set("US_theftsuccess", $user->info->US_theftsuccess + 1);
			} else {
				$user->set("US_theftfail", $user->info->US_theftfail + 1);
			}
		}
		if($data['module'] == "chase") {
			$user->set("US_chasedone", $user->info->US_chasedone + 1);
			if($data['success'] == "true"){
				$user->set("US_chasesuccess", $user->info->US_chasesuccess + 1);
			} else {
				$user->set("US_chasefail", $user->info->US_chasefail + 1);
			}
		}
		if($data['module'] == "jail") {
			$user->set("US_bustsdone", $user->info->US_bustsdone + 1);
			if($data['success'] == "true"){
				$user->set("US_bustsuccess", $user->info->US_bustsuccess + 1);
			} else {
				$user->set("US_bustsfail", $user->info->US_bustsfail + 1);
			}
		}
		if($data['module'] == "travel") {
			if($data['success'] == "true"){
				$user->set("US_traveltotal", $user->info->US_traveltotal + 1);
			}
		}
	});
?>