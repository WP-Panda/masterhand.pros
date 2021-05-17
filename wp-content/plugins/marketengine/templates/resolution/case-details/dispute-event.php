<div class="me-dispute-event">
    <h3><?php _e("Dispute Event", "enginethemes"); ?></h3>
    <?php
        $message_query = new ME_Message_Query(array('post_type' => 'revision', 'post_parent' => $case->ID, 'showposts' => -1));
        $revisions = $message_query->posts;
    ?>

    <?php foreach ($revisions  as $key => $message) : ?>
    <a href="#message-<?php echo $message->ID; ?>" id="event-<?php echo $message->ID; ?>">
        <?php switch ($message->post_status) {
            case 'me-closed':
                _e("<span>Close dispute</span>", "enginethemes");
                break;
            case 'me-escalated' :
                _e("<span>Escalate dispute</span>", "enginethemes");
                break;
            case 'me-waiting' :
                    _e("<span>Close dispute request</span>", "enginethemes");
                break;
            case 'me-open' :
                _e("<span>Dispute started</span>", "enginethemes");
                break;
            case 'me-resolved' :
                _e("<span>Dispute Resolved</span>", "enginethemes");
                break;
        }
        ?>
        <span><?php echo date_i18n( get_option('date_format'),  strtotime($message->post_date) ); ?></span>
    </a>
    <?php endforeach; ?>
</div>