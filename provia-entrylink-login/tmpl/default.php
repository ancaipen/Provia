
<?php echo $error_messages; ?>
<form action="/entry-link-login/?type=login" method="POST">
<div class="entrylink-login-body">
<div class="um-row _um_row_1 " style="margin: 0 0 30px 0;">
<div class="um-col-1">

<div id="um_field_6_username" class="um-field um-field-text  um-field-username um-field-text um-field-type_text" data-key="username">
<div class="um-field-label"><label for="username-6">Username or E-mail</label>
<div class="um-clear"></div></div>
<div class="um-field-area">
<input autocomplete="off" class="um-form-field valid " type="text" name="username-6" id="username-6" value="" placeholder="" data-validate="unique_username_or_email" data-key="username">
</div>
</div>

<div id="um_field_6_user_password" class="um-field um-field-password  um-field-user_password um-field-password um-field-type_password" data-key="user_password">
<div class="um-field-label">
<label for="user_password-6">Password</label>
<div class="um-clear"></div>
</div><div class="um-field-area">
<input class="um-form-field valid " type="password" name="user_password-6" id="user_password-6" value="" placeholder="" data-validate="" data-key="user_password">
</div>
</div>

</div>
</div>
						
<div class="um-col-alt">

	<div class="um-field um-field-c">
		<div class="um-field-area">
			<label class="um-field-checkbox">
				<input type="checkbox" name="rememberme" value="1">
				<span class="um-field-checkbox-state"><i class="um-icon-android-checkbox-outline-blank"></i></span>
				<span class="um-field-checkbox-option"> Keep me signed in</span>
			</label>
		</div>
	</div>

				<div class="um-clear"></div>

	<div class="um-left um-half">
		<input type="submit" value="Login" class="um-button" id="um-submit-btn">
	</div>
	<div class="um-right um-half">
		<a href="/register/" class="um-button um-alt">
			Register				</a>
	</div>

		
	<div class="um-clear"></div>

</div>
</div>	
</form>