<div class="row">
	<div class="col-md-9">

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo $panel_title ?></h3>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-3"><?php echo get_nav('panel') ?></div>
			<div class="col-md-9">
				<?php if ( isset( $count_log ) ) echo '<p>'.$count_log.'</p>' ?>
				<?php if ( isset( $panel_body ) ) echo $panel_body ?>
			</div>
		</div>
	</div>
</div>

	</div>
	<div class="col-md-3">
		<?php echo get_nav('side') ?>
	</div>
</div>
