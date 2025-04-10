/**
 * Settings scripts
 */
jQuery(document).ready(function ($) {
    // #ttp-force_refresh_token confirm
    (() => {
        const anchor = $('#ttp-force_refresh_token')
        anchor.on('click', () => {
            if (!confirm('Are you sure?')) {
                return false;
            }
        })
    })()
})
