<div class="panel panel-<?php echo esc_attr( $panel->type ); ?>">
    <?php if ( ! empty( $panel->title ) ) : ?>
    <div class="panel-heading"><h3 class="panel-title"><?php echo esc_html( $panel->title ); ?></h3></div>
    <?php endif; ?>
    <div class="panel-body"><?php echo esc_html( $panel->content ); ?></div>
    <?php if ( ! empty( $panel->footer ) ) : ?>
    <div class="panel-footer"><?php echo esc_html( $panel->footer ); ?></div>
    <?php endif; ?>
</div>

