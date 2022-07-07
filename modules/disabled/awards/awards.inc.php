<?php
	class awards extends module {

		public $allowedMethods = array();
	
		private function getAward($id = null) {
			global $user;
            $awards = $this->db->prepare("
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
				$awards = $awards->fetchAll(PDO::FETCH_ASSOC);
				foreach ($awards as $key => $award) {
					$type = "US_".preg_replace("/\s+/", "", strtolower($award['type']));
					$award['progress'] = $user->info->$type ." / ". $award['required'];
					$award['progressperc'] = ($user->info->$type != 0 ? ($user->info->$type / $award['required'] * 100) : 0);
					$award['completed'] = ($award['progressperc'] >= 100 ? 1 : 0);
					$awards[$key] = $award;
				}
			}else{
				$awards = $awards->fetch(PDO::FETCH_ASSOC);
			}
			return $awards;
        }
		
		public function getUserAward($id)
		{
			$awards = $this->db->prepare ("
				SELECT 
					UA_user as 'user', 
					UA_award as 'award',
					UA_time as 'time'
				FROM
					userAwards
				WHERE 
					UA_user = :user
				ORDER BY 
					UA_time 
				DESC");
			$awards->bindParam(":user", $id);
			$awards->execute();     
			return $awards->fetchAll(PDO::FETCH_ASSOC);	 
		}
   
		public function constructModule() {
			$userAwards = array();
			$awards = $this->getUserAward($this->user->id);
			foreach ($awards as $award) {
				$userAwards[] = array(
                    "award" => $this->getAward($award["award"]), 
                    "time" => date('d M Y', $award["time"])
                );
			}
			$this->html .= $this->page->buildElement("awardMain", array(
				"awards" => $this->getAward(),
				"userAwards" => $userAwards
			));
		}
	}