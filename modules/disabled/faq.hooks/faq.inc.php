<?php

    class faq extends module {
        
      public $allowedMethods = array();
        public function constructModule() {
        global $db, $page;

 
            
            


               $properties = $this->db->selectAll("
				SELECT
					PR_id as 'id', PR_location as 'location', PR_module as 'type', PR_user as 'owner'
				FROM
					properties 
				ORDER BY
					`PR_location` DESC
				Limit 3
            ");
        $this->html .= $this->page->buildElement("faq", array());

        
        
        }}

?>