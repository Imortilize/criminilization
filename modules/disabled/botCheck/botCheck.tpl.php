<?php

    class botCheckTemplate extends template {
        
        public $botCheck = '
            <div class="panel panel-default">
                <div class="panel-heading">
                    Are you human?
                </div>
                <div class="panel-body">
                    <p>
                        Please enter the code below to continue playing!
                    </p>
                    <p>
                        <form action="?page=botCheck&action=check" method="post">
                            <img src="{image_src}" />
                            <input type="number" class="form-control form-control-inline" name="code" style="width: 75px" />
                            <button class="btn btn-default">
                                Unlock
                            </button>
                        </form>
                    </p>
                </div>
            </div>


        ';
        
    }

?>