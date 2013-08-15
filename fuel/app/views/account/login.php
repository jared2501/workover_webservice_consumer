<div class="row-fluid">
	<div class="span4">
		&nbsp;
	</div>
	<div class="span4">
		<div class="box">
			<div class="box-title">
				Log In
			</div>
			<div class="box-content">
				
				<?php if(isset($flash['errorText'])): ?>
					<p class="alert"><?php echo $flash['errorText']; ?></p>
				<?php endif; ?>

				<form method="post" class="form-horizontal" style="margin-bottom: 0;">
					<div class="control-group">
						<label class="control-label" for="usernameOrEmail">Username/Email</label>
						<div class="controls">
							<input id="usernameOrEmail" type="text" class="input-block-level" name="username_or_email">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="password">Password</label>
						<div class="controls">
							<input id="password" type="password" class="input-block-level" name="password">
						</div>
					</div>
					<div class="form-actions" style="margin-bottom: 0;">
						<input type="submit" class="btn btn-success" value="Log In">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>