<?php

    class fiftyfiftyTemplate extends template {

        
        public $fiftyfifty = '
            <div class="row">
                <div class="col-md-12">
                    <form action="?page=fiftyfifty&action=bet" method="post">
                        <div class="panel panel-default">
                            <div class="panel-heading">50/50 Game</div>
                            <div class="panel-body">
                                <p style="height:54px; line-height:18px;">
                                    Feeling Lucky? Put your money where your mouth is and take on the house!</p>
                                <p>
                                    <input type="text" class="form-control" value="{bet}" name="bet" />
                                </p>
                                <p class="text-right">
                                    <button type="submit" class="btn btn-default" name="submit" value="submit">Bet!</button>
                                </p>
  <small> Min: $100 Max: {maxBet}</small> <br />
                        <small>{>propertyOwnership}</small>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        ';
        
    }

?>
