<?php

/**
* A new home page for your game rather then the default login screen.
*
* @package Home Page
* @author NIF
* @version 1.0.0
*/

class homeTemplate extends template {

	public $home = '
		<div class="text-center">

			{#if login}

				<form action="?page=login&action=login" method="post">
					<div class="row">
						<div class="col-xs-5 text-left">
							<input autocomplete="new-password" type="input" class="form-control" name="email" placeholder="Email" />
						</div>
						<div class="col-xs-5 text-left">
							<input autocomplete="new-password" type="password" class="form-control" name="password" placeholder="Password" />
						</div>
						<div class="col-xs-2">
							<div class="text-right">
								<button type="submit" class="btn btn-default btn-block">
									Login
								</button>
							</div>
						</div>
					</div>
				</form>
			{/if}

			{#if loginCustomBBCode}
				<hr />
				[{loginCustomBBCode}]
			{/if}

			{#if news}
				<hr />
				<h3>
					{news.title} <br />
					<small class="tiny">
						By {>userName news} on {news.date}
					</small>
				</h3>
				<div class="well well-sm">
					[{news.text}]
				</div>
			{/if}

			{#if top4}
				<hr />
				<h3>Top 4 players</h3>
				<div class="row">
					{#each top4}
						<div class="col-md-3">
							{>userName}<br />
							<img src="{user.profilePicture}" alt="{user.name}" class="img-circle img-thumbnail" />
						</div>
					{/each}
				</div>
			{/if}
			<hr />
			<div class="row">
				<div class="col-md-4">
					{#if loginScreenshot1}
						<p>
							<img src="{loginScreenshot1}" class="img-thumbnail" />
						</p>
					{/if}
					{#if loginScreenshot1text}
						<div class="well well-sm">
							[{loginScreenshot1text}]
						</div>
					{/if}
				</div>
				<div class="col-md-4">
					{#if loginScreenshot2}
						<p>
							<img src="{loginScreenshot2}" class="img-thumbnail" />
						</p>
					{/if}
					{#if loginScreenshot2text}
						<div class="well well-sm">
							[{loginScreenshot2text}]
						</div>
					{/if}
				</div>
				<div class="col-md-4">
					{#if loginScreenshot3}
						<p>
							<img src="{loginScreenshot3}" class="img-thumbnail" />
						</p>
					{/if}
					{#if loginScreenshot3text}
						<div class="well well-sm">
							[{loginScreenshot3text}]
						</div>
					{/if}
				</div>
			</div>
		</div>
	';


    public $options = '

        <form method="post" action="?page=admin&module=home&action=settings">

            <div class="row">
                <div class="col-md-4">
                    <label class="">Top 4 Players</label>
                    <div class="form-group">
                        <select class="form-control" name="showTop4Players" data-value="{showTop4Players}">
                        	<option value="1">Show</option>
                        	<option value="0">Hide</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="">Lates News</label>
                    <div class="form-group">
                        <select class="form-control" name="showLatestNews" data-value="{showLatestNews}">
                        	<option value="1">Show</option>
                        	<option value="0">Hide</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="">Login Form</label>
                    <div class="form-group">
                        <select class="form-control" name="showLogin" data-value="{showLogin}">
                        	<option value="1">Show</option>
                        	<option value="0">Hide</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr />

            <div class="row">
                <div class="col-md-4">
                    <label class="">loginScreenshot 1 URL</label>
                    <div class="form-group">
                        <input type="text" class="form-control"  name="loginScreenshot1" value="{loginScreenshot1}" />
                    </div>
                    <label class="">loginScreenshot 1 Text</label>
                    <div class="form-group">
                        <textarea type="text" class="form-control"  name="loginScreenshot1text" rows="3">{loginScreenshot1text}</textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="">loginScreenshot 2 URL</label>
                    <div class="form-group">
                        <input type="text" class="form-control"  name="loginScreenshot2" value="{loginScreenshot2}" />
                    </div>
                    <label class="">loginScreenshot 2 Text</label>
                    <div class="form-group">
                        <textarea type="text" class="form-control"  name="loginScreenshot2text" rows="3">{loginScreenshot2text}</textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="">loginScreenshot 3 URL</label>
                    <div class="form-group">
                        <input type="text" class="form-control"  name="loginScreenshot3" value="{loginScreenshot3}" />
                    </div>
                    <label class="">loginScreenshot 3 Text</label>
                    <div class="form-group">
                        <textarea type="text" class="form-control"  name="loginScreenshot3text" rows="3">{loginScreenshot3text}</textarea>
                    </div>
                </div>
            </div>


            <label class="">Custom BBCode</label>
            <div class="form-group">
                <textarea type="text" class="form-control"  name="loginCustomBBCode" rows="5">{loginCustomBBCode}</textarea>
            </div>

            <div class="text-right">
                <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
            </div>
        </form>
    ';
}
