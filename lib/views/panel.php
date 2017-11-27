<div class="panel panel-<?php echo esc_attr( $panel->type ); ?>">
    <?php if ( 'no' != $panel->title ) : ?>
    <div class="panel-heading"><?php echo esc_html( $panel->title ); ?></div>
    <?php endif; ?>
    <div class="panel-body"><?php echo esc_html( $panel->content ); ?></div>';
    <?php if ( 'no' != $panel->footer ) : ?>
    <div class="panel-footer"><?php echo esc_html( $panel->footer ); ?></div>
    <?php endif; ?>
</div>

