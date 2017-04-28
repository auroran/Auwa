<header>
	<button class="fa fa-save" role="SaveUser" data-id="{$user->id}"></button>
</header>
<article>
	<section>
		<fieldset>
			<label>Login</label>
			<div>
				<input type="text" value="{$user->login}" name="login">
			</div>
		</fieldset>
		<fieldset>
			<label>Identité</label>
			<div>
				<input type="text" value="{$user->getIdentity()}" name="name">
			</div>
		</fieldset>
		<fieldset>
			<label>Mail</label>
			<div>
				<input type="text" value="{$user->getMail()}" name="mail">
			</div>
		</fieldset>
		<fieldset>
			<label>Password</label>
			<div>
				<input type="text" value="{$user->getPasswd()}" name="passwd">
			</div>
		</fieldset>
		<fieldset>
			<label>Type de compte</label>
			<div>
				<select name="status">
					<option value="User"{if $user->getStatus()=='User'} selected="selected"{/if}>Standart</option>
					<option value="CoreUser"{if $user->getStatus()=='CoreUser'} selected="selected"{/if}>Admin</option>
					<option value="CoreRoot"{if $user->getStatus()=='CoreRoot'} selected="selected"{/if}>Super Admin</option>
				</select>
			</div>
		</fieldset>
		<fieldset>
			<label>Actif</label>
			<div>
				<select name="enable" class="btn-switch">
					<option value="0"{if !$user->isEnable()} selected="selected"{/if}>Non</option>
					<option value="1"{if $user->isEnable()} selected="selected"{/if}>Oui</option>
				</select>
			</div>
		</fieldset>
	</section>
</article>