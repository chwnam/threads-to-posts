<?php

use Bojaghi\Template\Template;

/**
 * @var Template $this
 *
 * Context:
 */
?>

<div class="wrap">
    <h1>Tester</h1>
    <hr class="wp-header-end">
    <div id="ttp-tester-posts" class="ttp-tester">
        <h3>Retrieve Posts from Threads</h3>
        <pre id="ttp-posts-response" class="ttp-raw_output"></pre>
        <p class="input-group">
            <button id="ttp-retrieve_posts"
                    class="button"
                    type="button"
                    data-action="ttp_tester"
                    data-nonce="<?php echo esc_attr(wp_create_nonce('ttp_tester')); ?>">Retrive
            </button>
        </p>
    </div>
    <div id="ttp-tester-single" class="ttp-tester">
        <h3>Retrieve Single Post from Threads</h3>
        <pre id="ttp-single-response" class="ttp-raw_output"></pre>
        <p class="input-group">
            <label for="ttp-single-id">ID</label>
            <input id="ttp-single-id" type="text">
            <button id="ttp-retrieve_single"
                    class="button"
                    type="button"
                    data-action="ttp_tester"
                    data-nonce="<?php echo esc_attr(wp_create_nonce('ttp_tester')); ?>">Retrieve
            </button>
        </p>
    </div>
    <div id="ttp-tester-conversations" class="ttp-tester">
        <h3>Retrieve Conversations from Single Post</h3>
        <pre id="ttp-converstions-response" class="ttp-raw_output"></pre>
        <p class="input-group">
            <label for="ttp-conversations-id">ID</label>
            <input id="ttp-conversations-id" type="text">
            <button id="ttp-retrieve_conversations"
                    class="button"
                    type="button"
                    data-action="ttp_tester"
                    data-nonce="<?php echo esc_attr(wp_create_nonce('ttp_tester')); ?>">Retrieve
            </button>
        </p>
    </div>
</div>
