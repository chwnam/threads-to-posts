jQuery(function ($) {
    const scrapMode = $('[name="ttp_scrap_mode"]'),
        submit = $('#submit'),
        initial = scrapMode.filter(':checked').val() || ''

    scrapMode.change(function () {
        const current = scrapMode.filter(':checked').val()
        submit.prop('disabled', current === initial)
    }).trigger('change')

    submit.click(function () {
        const current = scrapMode.filter(':checked').val()
        if (!confirm('Are you sure?')) {
            return false;
        }
    })
});