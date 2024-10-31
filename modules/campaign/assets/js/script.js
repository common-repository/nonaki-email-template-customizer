jQuery(document).ready(function ($) {
    $('#template_id').on('change', function () {
        let templateID = $('#template_id').val();
        if(templateID != ''){
            let preview = $('#nonaki-iframe');
            let link = nonaki_campaign_data.admin_url + '/index.php?page=nonaki&id=' + templateID + '&mood=preview'
            preview.attr('src',link)
        }
    })
});