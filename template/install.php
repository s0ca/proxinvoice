<div id='install' class='center'>
	<h2>Database informations</h2>
	<form method='post'>
		<table>
			<tr>
				<td>
					<label>database server (hostname) :</label>
				</td>
				<td>
					<input type='text' name='db_host' value='<?= $db_host ?>' />
				</td>
			</tr>
			<tr>
				<td>
					<label>database name :</label>
				</td>
				<td>
					<input type='text' name='db_name' value='<?= $db_name ?>' />
				</td>
			</tr>
			<tr>
				<td>
					<label>database user name:</label>
				</td>
				<td>
					<input type='text' name='db_user_name' value='<?= $db_user_name ?>' />
				</td>
			</tr>
			<tr>
				<td>
					<label>database user password :</label>
				</td>
				<td>
					<input type='password' name='db_user_passwd' value='<?= $db_user_passwd ?>' />
				</td>
			</tr>
			<tr>
				<td colspan='2' style='text-align: right'>
					<input type='submit' name='install_done' />
				</td>
			</tr>
		</table>
	</form>
</div>
