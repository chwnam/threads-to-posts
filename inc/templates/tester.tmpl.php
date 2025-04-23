<?php

use Chwnam\ThreadsToPosts\Vendor\Bojaghi\Template\Template;

/**
 * @var Template $this
 *
 * Context:
 * - status: string
 */
?>

<?php
$this
    ->extends('token-status-required')
    ->assign('title', 'API Tester')
    ->start('okay');
?>
    <div id="ttp-tester-posts" class="ttp-tester">
        <h3>Retrieve Posts from Threads</h3>
        <pre id="ttp-posts-response" class="ttp-raw_output"></pre>
        <div class="ttp-retrieve-coltrols">
            <button id="ttp-retrieve_posts"
                    class="button"
                    type="button"
                    data-action="ttp_tester"
                    data-nonce="<?php echo esc_attr(wp_create_nonce('ttp_tester')); ?>">Retrive
            </button>
            <div class="ttp-retrieve-option-group">
                <div class="ttp-retrieve-option-item">
                    <label for="ttp-posts-limit">Limit</label>
                    <input id="ttp-posts-limit" type="number" class="text" value="25">
                </div>
                <div class="ttp-retrieve-option-item">
                    <label for="ttp-posts-cursor-type">Cursor</label>
                    <select id="ttp-posts-cursor-type">
                        <option value="before">Before</option>
                        <option value="after">After</option>
                    </select>
                    <label for="ttp-posts-cursor-value" class="screen-reader-text">Cursor Value</label>
                    <input id="ttp-posts-cursor-value" type="text" class="text">
                </div>
            </div>
        </div>
    </div>
    <div id="ttp-tester-single" class="ttp-tester">
        <h3>Retrieve Single Post from Threads</h3>
        <pre id="ttp-single-response" class="ttp-raw_output"></pre>
        <div class="ttp-retrieve-coltrols">
            <button id="ttp-retrieve_single"
                    class="button"
                    type="button"
                    data-action="ttp_tester"
                    data-nonce="<?php echo esc_attr(wp_create_nonce('ttp_tester')); ?>">Retrieve
            </button>
            <div class="ttp-retrieve-options-group">
                <div class="ttp-retrieve-option-group">
                    <label for="ttp-single-id">ID</label>
                    <input id="ttp-single-id" type="text">
                </div>
            </div>
        </div>
    </div>
    <div id="ttp-tester-conversations" class="ttp-tester">
        <h3>Retrieve Conversations from Single Post</h3>
        <pre id="ttp-converstions-response" class="ttp-raw_output"></pre>
        <div class="ttp-retrieve-coltrols">
            <button id="ttp-retrieve_conversations"
                    class="button"
                    type="button"
                    data-action="ttp_tester"
                    data-nonce="<?php echo esc_attr(wp_create_nonce('ttp_tester')); ?>">Retrieve
            </button>
            <div class="ttp-retrieve-option-group">
                <div class="ttp-retrieve-option-item">
                    <label for="ttp-conversations-id">ID</label>
                    <input id="ttp-conversations-id" type="text">
                </div>
            </div>
        </div>
    </div>
<?php
$this->end();
