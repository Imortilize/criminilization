<?php
class fakePlayersTemplate extends template {

    public $newBot = '
        <form method="post" action="?page=admin&module=fakePlayers&action=new">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="pull-left">Number of Bots:</label>
                        <input type="number" class="form-control" name="bots" />
                    </div>
                </div>
            </div>
            <div class="text-right">
                <span class="pull-left" style="margin-top:10px;">Bots In Game: <strong>{bots}</strong></span>
                <button class="btn btn-default" name="submit" type="submit" value="1">Add Now</button>
            </div>
        </form>
    ';

    public $settings = '
        <form method="post" action="?page=admin&module=fakePlayers&action=settings">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="pull-left">Bots Passwords: <small>(Default password is: harbzali@gmail.com)</small></label>
                        <input type="text" class="form-control" name="botsPassword" value="{botsPassword}" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="pull-left">Organized crimes: <small>(Only if you have got the module)</small></label>
                        <select name="ocBots" data-value="{ocBots}" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Disabled</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="form-group">
                        <label class="pull-left">Buying bullets: <small>(Allow bots buy bullets from bullets factory, they will stop when stock is 2,000)</small></label>
                        <select name="botsBullets" data-value="{botsBullets}" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Disabled</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="pull-left">Attacking players <small>Allow bots to attack players (they will make only a little damage)</small></label>
                        <select name="botsAttack" data-value="{botsAttack}" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Disabled</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
            </div>
        </form>
    ';

    public $toUser = '
        <form method="post" action="?page=admin&module=fakePlayers&action=toUser">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="pull-left">Bot Name:</label>
                        <input type="text" class="form-control" name="user" value="{user}" />
                    </div>
                </div>
            </div>
            <div class="text-right">
                <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
            </div>
        </form>
    ';

}
