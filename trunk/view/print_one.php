<div id="r_wrap"> 
    <div style="float:left;padding:0px;margin-right:10px;margin-bottom:5px;">
        <a href="<?php echo($result2->rcu_sc_permalink_url); ?>" target="_blank">
            <img src="<?php echo esc_attr($result2->rcu_sc_avatar_url); ?>" border="0" width="50" height="50">
        </a>
    </div>
    <div style="float:left;margin:0px;padding:0px;">
        <div style="font-size:24px;margin:0px;padding:0px;"><?php echo($result2->rce_sct_title); ?></div>
        <div style="font-size:14px;margin:0px;padding:0px;">Uploaded by <a href="<?php echo($result2->rcu_sc_permalink_url); ?>" target="_blank"><?php echo($result2->rcu_sc_username); ?></a></div>

        <div class="r_vote">
                    <?php if( !empty($vote_results) ) { ?>
                            <div id="r-vote-<?php echo($result2->rce_id); ?>">Voted</div>
                            <div id="r_votes"><?php echo($result2->rce_vote_count); ?> votes</div>
                    <?php } else { ?>
                            <div id="r-vote-<?php echo($result2->rce_id); ?>"><a href="?rmxid=<?php echo($result2->rce_id); ?>&voting_rmx=<?php echo($result2->rce_id); ?>" id="vote-<?php echo($result2->rce_id); ?>">Vote</a></div>
                            <div id="r_votes"><?php echo($result2->rce_vote_count); ?> votes</div>
                    <?php } ?>
        </div>

    </div>
    <div id="clear"></div>
            <div id="facebook_p">
                    <div class="fb-like" data-href="<?php echo($current_rmx_url); ?>" data-send="false" data-layout="button_count" data-width="200" data-show-faces="false"></div>
            </div>
            <div id="twitter_p">
                    <a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo($current_rmx_url); ?>" data-text="Sound contest, listen to <?php echo $record['rce_sct_title']; ?>" data-via="kenrecords" data-related="djkentwist">Tweet</a>
                    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
            </div>
            <div id="google_p">
                    <g:plusone size="medium" href="<?php echo($current_rmx_url); ?>"></g:plusone>
            </div>
    <div id="r_stream">
    <iframe width="100%" height="166" scrolling="no" frameborder="no" src="http://w.soundcloud.com/player/?url=<?php echo($result2->rce_sct_secret_uri); ?>&amp;auto_play=false&amp;show_artwork=false&amp;color=000000"></iframe>
    </div>

    <div class="fb-comments" data-href="<?php echo($current_rmx_url); ?>" data-num-posts="<?php echo($kenrmx_facebook_comments); ?>" data-width="<?php echo($kenrmx_facebook_width); ?>"></div>
</div>
