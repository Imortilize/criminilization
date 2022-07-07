<?php

class loggedinTemplate extends template {

    public $newsArticle = '
            <div class="row">
                <div class="col-lg-9">
                    <div class="panel panel-default">
                        <div class="panel-heading">Game Updates</div>
                        <div class="panel-body">
                            {#each news}
                            <div class="crime-holder">
                                <p>
                                    <span class="action">
                                    <span class="iconify {color}" data-icon="{icon}"></span>
                                    {title}
                                    </span>
                                    <span class="cooldown">
                                        {date}
                                    </span>
                                    <span class="commit">
                                        <a href="#news_{id}" data-toggle="collapse">
                                            Read
                                        </a>
                                    </span>
                                </p>
                            </div>
                            <div class="collapse panel panel-default panel-body" id="news_{id}">
                                <p>[{text}]</p>
                            </div>
                            {/each}
                            {#unless news}
                            <p>There are no updates yet.</p>
                            {/unless}
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel panel-default">
                        <div class="panel-heading">Keys Info</div>
                        <div class="panel-body p-0">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item ">
                                    <span class="iconify text-secondary" data-icon="bx:bx-news"></span>
                                    <span class="align-middle text-secondary">News</span>
                                </li>
                                <li class="list-group-item ">
                                    <span class="iconify text-success" data-icon="carbon:update-now"></span>
                                    <span class="align-middle text-success">Update</span>
                                </li>
                                <li class="list-group-item ">
                                    <span class="iconify text-warning" data-icon="zondicons:announcement"></span>
                                    <span class="align-middle text-warning">Announcement</span>
                                </li>
                                <li class="list-group-item ">
                                    <span class="iconify text-danger" data-icon="akar-icons:bug"></span>
                                    <span class="align-middle text-danger">Bug Fix</span>
                                </li>
                                <li class="list-group-item ">
                                    <span class="iconify text-info" data-icon="grommet-icons:new"></span>
                                    <span class="align-middle text-info">Feature</span>
                                </li>
                                <li class="list-group-item ">
                                    <span class="iconify text-primary" data-icon="bx:bxs-offer"></span>
                                    <span class="align-middle text-primary">Offer</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>


        ';


    public $loggedinList = '
            <table class="table table-condensed table-responsive table-striped table-bordered">
                <thead>
                    <tr>
                        <th width="150px">Author</th>
                        <th>Title</th>
                        <th width="160px">Date</th>
                        <th width="120px">Options</th>
                    </tr>
                </thead>
                <tbody>
                    {#each loggedin}
                        <tr>
                            <td>{gnauthor}</td>
                            <td>{gntitle}</td>
                            <td>{gndate}</td>
                            <td>
                                [<a href="?page=admin&module=loggedin&action=edit&id={id}">Edit</a>]
                                [<a href="?page=admin&module=loggedin&action=delete&id={id}">Delete</a>]
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        ';

    public $loggedinDelete = '
            <form autocomplete="off" method="post" action="?page=admin&module=oggedin&action=delete&id={id}&commit=1">
                <div class="text-center">
                    <p> Are you sure you want to delete this news post?</p>

                    <p><em>"{gntitle}"</em></p>

                    <button class="btn btn-danger" name="submit" type="submit" value="1">Yes delete this news post</button>
                </div>
            </form>

        ';


    public $loggedinNewForm = '
            <form autocomplete="off" method="post" action="?page=admin&module=loggedin&action={editType}&id={id}">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="pull-left">Title</label>
                            <input type="text" class="form-control" name="gntitle" value="{gntitle}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">
                                Type
                            </label>
                            <select class="form-control" name="type" data-value="{type}">
                                <option value="update">Update</option>
                                <option value="news">News</option>
                                <option value="announcement">Announcement</option>
                                <option value="bug">Bug Fix</option>
                                <option value="feature">Feature</option>
                                <option value="offer">Offer</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="pull-left">Text</label>
                    <textarea rows="8" type="text" class="form-control" name="gntext">{gntext}</textarea>
                </div>
                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>
            </form>
        ';
}

