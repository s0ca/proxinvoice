<hr class='separator' />
<div id='diff' class='center'>
	<table>
		<tr>
			<?= "<td rowspan='" . (count($content['diff']) + 1) ."'>" ?>
				<label>Match :</label><br />
				<?php foreach($content['match'] as $fr => $cn) : ?>
					<?= "$fr :" ?>
					<?php foreach($cn as $ref) : ?>
						<?= " $ref" ?>
					<?php endforeach ?>
					<?= '<br />' . PHP_EOL ?>
				<?php endforeach ?>
			</td>
			<td></td><td><label>French file :</label></td><td><label>Chinese file :</label></td>
		</tr>
		<?php foreach($content['diff'] as $case => $files) : ?>
			<?php if (!$files['diff']) : ?>
				<tr class='success'>
			<?php else : ?>
				<tr class='error'>
			<?php endif ?>
			<td><label><?= $case ?></label></td>
			<td>
			<?php foreach($files['FR'] as $refFR => $quantity) : ?>
				<?= "$refFR x $quantity</br>" . PHP_EOL ?>
			<?php endforeach ?>
			</td>
			<td>
			<?php foreach($files['CN'] as $refCN => $quantity) : ?>
				<?= "$refCN x $quantity</br>" . PHP_EOL ?>
			<?php endforeach ?>
			</td>
		</tr>
		<?php endforeach ?>
	</table>
</div>
