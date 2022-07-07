<?php

    class suicideTemplate extends template {

        public $commitSuicide = '
            <div class="panel panel-default">
                <div class="panel-heading">
                    Commit Suicide
                </div>
                <div class="panel-body text-center">

                    <p>
                        If you commit suicide you will lose all of your stats and you can choose to start fresh with a new character!
                    </p>
                    <p>
                        To commit suicide please enter your password below.
                    </p>

                    <p>
                        <form method="POST" action="?page=suicide&action=commit">
                            <input type="password" name="password" class="form-control form-control-inline" />
                            <button class="btn btn-danger">
                                Commit suicide
                            </button>
                        </form>
                    </p>

                </div>
            </div>
        ';
        
    }

?>