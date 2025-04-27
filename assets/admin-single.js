jQuery(function ($) {
    const button = $('#ttp-fetch-article'),
        articleResult = $('#ttp-fetch-article-result')

    button.on('click', (e) => {
        e.preventDefault()
        wp.ajax.send({
            data: {
                action: 'ttp_fetch_article',
                threads_id: button.data('id'),
                nonce: button.data('nonce'),
            },
            beforeSend: () => {
                button.prop('disabled', true)
            },
            method: 'get',
            url: ajaxurl,
        }).done((data) => {
            const text = JSON.stringify(data.result, null, 2)
            articleResult.text(text)
        }).always(() => {
            button.prop('disabled', false)
        })
    });
})
