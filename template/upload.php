<div id='upload' class='center' >
	<h2>Upload files:</h2>
	<form method='post' enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="<?= $content['maxFileSize'] ?>">
		<table>
			<tr>
				<td><label>Francais :</label></td><td><label>Chinois :</label></td>
			</tr>
			<tr>
				<td><input type='file' name='FR' /></td><td><input type='file' name='CN' /></td>
			</tr>
			<tr>
				<td colspan='2'><input type='submit' class='center' name='upload' /></td>
			</tr>
		</table>
	</form>
</div>
