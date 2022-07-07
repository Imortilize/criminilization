<?php

/**
* This module allows people to get married
*
* @package Marriage
* @author Chris Day
* @version 1.0.0
*/

class marriageTemplate extends template {

	public $divorce = '
		<p>
			{>userName} has requested a divorce!
		</p>
		<p class="text-right">
			<a href="?page=marriage&action=divorce&id={user.id}&decline={code}" class="btn btn-danger">
				Decline
			</a>
			<a href="?page=marriage&action=divorce&id={user.id}&accept={code}" class="btn btn-success">
				Accept
			</a>
		</p>
	';

	public $proposal = '
		<p>
			{>userName}: [{text}]
		</p>
		<p class="text-right">
			<a href="?page=marriage&action=decline&id={user.id}" class="btn btn-danger">
				Decline
			</a>
			<a href="?page=marriage&action=accept&id={user.id}" class="btn btn-success">
				Accept
			</a>
		</p>
	';

	public $marriage = '
		<div class="row">
			<div class="col-md-3"></div>
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						Marriage
					</div>
					<div class="panel-body">

						{#if married}
							<p>
								You are married to {>userName}!
							</p>
							<p>
								<small>
									<a href="?page=marriage&action=divorce">
										Request a divorce
									</a>
								</small>
							</p>

						{else}

							<p>
								You are not married to anyone!
							</p>
							<p>
								A marriage proposal will cost you ${number_format proposeCost}, if they decline you can sell the ring for ${number_format proposeRefund}
							</p>
							<form method="post" action="?page=marriage&action=propose">
								<p>
									<input type="text" class="form-control" name="user" placeholder="Username ..." />
								</p>
								<p>
									<textarea type="text" class="form-control" name="proposal" placeholder="Proposal Text ..." rows="3"></textarea>
								</p>
								<p>
									<button class="btn btn-default">
										Propose
									</button>
								</p>
							</form>

						{/if}
					</div>
				</div>
			</div>
		</div>

	';

}
