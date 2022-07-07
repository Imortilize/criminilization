<?php

	class crackTheSafeTemplate extends template {

		public $safe = '
			<div class="row">
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							{location} Safe
						</div>
						<div class="panel-body text-center">
							<form action="?page=crackTheSafe&action=guess" method="post">
								<h3>
									Prize: ${number_format prize}
								</h3>
								<p>
									<small> Last cracked {date}</small>
								</p>
								<hr />
								<p>
									<input type="number" class="form-control form-control-inline" name="pin" placeholder="Enter PIN Code" />
								</p>
								<p>
									<button class="btn btn-default">
										Try PIN Code
									</button>
								</p>
							</form>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							Your last 10 guesses*
						</div>
						<div class="list-group">
							{#each guesses}
								<div class="list-group-item text-left">
									{#if higher}Higher then{/if}
									{#if lower}Lower then{/if}
									{number_format pin}
									<small class="pull-right">
										{_ago time} ago
									</small>
								</div>
							{/each}
						</div>
						<div class="panel-body">
							<small>* Each time you travel this resets</small>
						</div>
					</div>
				</div>
			</div>
		';

	}

