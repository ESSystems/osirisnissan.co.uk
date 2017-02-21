<?php if (!empty($persons)) : ?>
<ul>
	<?php foreach ($persons as $p) : ?>
		<?php $p = $p['Person']; ?>
		<li id="<?=$p['id']?>"><span class="informal"><?=$p['title']?> </span><?="{$p['first_name']} {$p['middle_name']} {$p['last_name']}" ?><div class="informal">
			<address>
				<?=$p['county']?> <?=$p['postcode']?><br/>
				<?=$p['address1']?><br/>
				<?=$p['address2']?><br/>
				<?=$p['address3']?><br/>
				<?php if (!empty($p['telephone_number'])) : ?>
					Telephone:
					<?php if (!empty($p['area_code'])) : ?>
						(<?=$p['area_code']?>)
					<?php endif; ?>
					<?=$p['telephone_number']?>
					<br/>
				<?php endif; ?>
				<?php if (!empty($p['email_address'])) : ?>
					Email: <?=$p['email_address']?>
				<?php endif; ?>
			</address>
		</div></li>
	<?php endforeach; ?>
</ul>
<?php else : ?>

<?php endif;?>