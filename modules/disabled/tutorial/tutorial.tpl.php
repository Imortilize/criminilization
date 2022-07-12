<?php
class tutorialTemplate extends template {

    public $tutorialsList = '
    <table class="table table-condensed table-striped table-bordered table-responsive">
        <thead>
            <tr>
                <th>module</th>
                <th width="100px">Actions</th>
            </tr>
        </thead>
        <tbody>
            {#each tutorial}
                <tr>
                    <td>{mod}</td>
                    <td>
                        [<a href="?page=admin&module=tutorial&action=edit&id={id}">Edit</a>]
                        [<a href="?page=admin&module=tutorial&action=delete&id={id}">Delete</a>]
                    </td>
                </tr>
            {/each}
        </tbody>
    </table>
    ';

    public $tutorialForm = '
    <form method="post" action="?page=admin&module=tutorial&action={editType}&id={id}">
        <div class="form-group">
            <label class="pull-left">Tutorial Module</label>
            <select name="mods" class="form-control" data-value="{mod}">
                {#each modules}
                <option value="{id}">{name}</option>
                {/each}
            </select>
        </div>
        <div class="form-group">
            <label class="pull-left">Tutorial Text</label>
            <textarea class="form-control" name="text" rows="3">{text}</textarea>
        </div>
        <div class="text-right">
            <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
        </div>
    </form>
    ';

    public $tutorialDelete = '
    <form method="post" action="?page=admin&module=tutorial&action=delete&id={id}&commit=1">
        <div class="text-center">
            <p> Are you sure you want to delete this tutorial?</p>
            <p><em>"{module}"</em></p>
            <button class="btn btn-danger" name="submit" type="submit" value="1">Yes delete this tutorial</button>
        </div>
    </form>
    ';

}
