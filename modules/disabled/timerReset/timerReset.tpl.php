<?php

   class timerResetTemplate extends template {


        public $settings = '

            <form method="post" action="#">

                <p>To disable a timer from being reset set its value to 0</p>

                <div class="row">
                       {#each timers}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="pull-left">{_setting "pointsName"} to reset {name} timer</label>
                                <input type="text" class="form-control" name="{name}" value="{value}" />
                            </div>
                        </div>
                    {/each}
                </div>
                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>
            </form>
        ';

    }