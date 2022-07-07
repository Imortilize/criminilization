<?php
class fiftyfifty extends module
{
    public $maxBet = 100;
    public $allowedMethods = array('bet' => array('type' => 'post'), 'submit' => array('type' => 'post'));
    public function constructModule()
    {
        $this->property = new Property($this->user, "fiftyfifty");
        $options        = $this->property->getOwnership();
        if ($options["cost"])
            $this->maxBet = $options["cost"];
        $options["maxBet"] = $this->money($this->maxBet);
        if ($this->maxBet < $this->user->info->US_money) {
            $options["bet"] = '$' . number_format($this->maxBet);
        } else {
            $options["bet"] = '$' . number_format($this->user->info->US_money);
        }
        $this->html .= $this->page->buildElement("fiftyfifty", $options);
        $this->property = new Property($this->user, "fiftyfifty");
    }
    public function method_bet()
    {
        $this->property = new Property($this->user, "fiftyfifty");
        $this->property = new Property($this->user, "fiftyfifty");
        $options        = $this->property->getOwnership();
        if ($options["cost"])
            $this->maxBet = $options["cost"];
        $options["maxBet"] = $this->money($this->maxBet);
        if (isset($this->methodData->submit)) {
            $bet = str_replace(array(
                ',',
                '$'
            ), array(
                '',
                ''
            ), $this->methodData->bet);
            if ($bet > $this->user->info->US_money) {
                return $this->error("You don't have enough money to make that bet");
            }
            if ($bet < 100) {
                return $this->error("You need to bet more than the minimum bet!");
            }
            if ($bet > $this->maxBet) {
                return $this->error("The max bet is " . $this->money($this->maxBet));
            }
            $rand = mt_rand(1, 2);
            if ($rand == 1) {
                $user = $this->db->prepare("
UPDATE userStats SET 
US_money = US_money + :bet
WHERE 
US_id = :id
");
                $user->bindParam(":bet", $bet);
                $user->bindParam(":id", $this->user->info->US_id);
                $user->execute();
                $this->property->updateProfit(+$bet);
                $this->user->set("US_money", $this->user->info->US_money - $bet);
                return $this->error("You lost you bet of $" . number_format($bet) . ", better luck next time");
            } else {
                if ($options["user"]) {
                    $owner = new User($options["user"]["id"]);
                    if ($bet > $owner->info->US_money) {
                        $this->property->transfer($this->user->id);
                        return $this->error("The owner did not have enough cash to pay the bet, you took ownership of the casino.");
                    } else {
                        $userr = $this->db->prepare("
UPDATE userStats SET 
US_money = US_money - :bet
WHERE 
US_id = :owner
");
                        $userr->bindParam(":bet", $bet);
                        $userr->bindParam(":owner", $owner->id);
                        $userr->execute();
                    }
                }
                $this->user->set("US_money", $this->user->info->US_money + $bet);
                $newbet = $bet * 2;
                $this->property->updateProfit(-$bet);
                $this->error("You won $" . number_format($newbet) . ", well done!", "success");
            }
        }
    }
    public function method_own()
    {
        $this->property = new Property($this->user, "fiftyfifty");
        $owner          = $this->property->getOwnership();
        if ($owner["user"]) {
            $user = $this->page->buildElement("userName", $owner);
            return $this->html .= $this->page->buildElement("error", array(
                "text" => "This property is owned by " . $user
            ));
        }
        if ($this->user->info->US_money < 1000000) {
            return $this->html .= $this->page->buildElement("error", array(
                "text" => "You need $1,000,000 to buy of this property."
            ));
        }
        $update = $this->db->prepare("
UPDATE userStats SET US_money = US_money - 1000000 WHERE US_id = :id
");
        $update->bindParam(":id", $this->user->id);
        $update->execute();
        $this->property->transfer($this->user->id);
        return $this->html .= $this->page->buildElement("success", array(
            "text" => "You paid $1,000,000 to buy this property."
        ));
    }
}
?>