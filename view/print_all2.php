<div id="r_wrap"> 
    <div style="float:left;padding:0px;margin-right:10px;margin-bottom:5px;">
        <a href="<?php echo($result->rcu_sc_permalink_url); ?>" target="_blank">
            <img src="<?php echo esc_attr($result->rcu_sc_avatar_url); ?>" border="0" width="50" height="50">
        </a>
    </div>
    <div style="float:left;margin:0px;padding:0px;">
        <div style="font-size:24px;margin:0px;padding:0px;"><a href="<?php echo($base_url_remixers); ?>"><?php echo($result->rce_sct_title); ?></a></div>
        <div style="font-size:14px;margin:0px;padding:0px;">Uploaded by <a href="<?php echo($result->rcu_sc_permalink_url); ?>" target="_blank"><?php echo($result->rcu_sc_username); ?></a></div>

        <div class="r_vote">
                    <?php if( !empty($vote_results_all) ) { ?>
                            <div id="r-vote-<?php echo($result->rce_id); ?>">Voted</div>
                            <div id="r_votes"><?php echo($result->rce_vote_count); ?> votes</div>
                    <?php } else { ?>
                            <div id="r-vote-<?php echo($result->rce_id); ?>"><a href="?rmxid=<?php echo($result->rce_id); ?>&voting_rmx=<?php echo($result->rce_id); ?>" id="vote-<?php echo($result->rce_id); ?>">Vote</a></div>
                            <div id="r_votes"><?php echo($result->rce_vote_count); ?> votes</div>
                    <?php } ?>
        </div>

    </div>
    <div id="clear"></div>
    <div id="r_stream">
        <object height="18" width="100%"><param name="movie" value="https://player.soundcloud.com/player.swf?url=<?php echo($result->rce_sct_secret_uri); ?>&amp;color=000000&amp;auto_play=false&amp;player_type=tiny"></param><param name="allowscriptaccess" value="always"></param><param name="wmode" value="transparent"></param><embed wmode="transparent" allowscriptaccess="always" height="18" width="100%" src="https://player.soundcloud.com/player.swf?url=<?php echo($result->rce_sct_secret_uri); ?>&amp;color=000000&amp;auto_play=false&amp;player_type=tiny"></embed></object>
    </div>
</div>