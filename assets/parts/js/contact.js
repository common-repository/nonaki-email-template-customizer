jQuery(document).ready(function ($) {


    console.log(nonaki_contact_data)
    $('#bulk-action-selector-top').on('change', function () {
        console.log($('#bulk-action-selector-top').val())
        if ($('#bulk-action-selector-top').val() == 'send_email') {
            console.log(nonaki_contact_data)
            console.log(nonaki_contact_data.template_selection_dropdown)
            $(nonaki_contact_data.template_selection_dropdown).insertAfter($('#bulk-action-selector-top'));

            $('#email_type').on('change', function () {
                if ($('#email_type').val()) {
                    $('#email_subject').show()
                    if ($('#email_type').val() === 'text') {
                        $('#message').show();
                        $('#email_template_id').hide();
                    } else {
                        $('#message').hide();
                        $('#email_template_id').show();
                    }
                } else {
                    $('#email_subject').hide()
                    $('#email_template_id').hide();
                    $('#message').hide();
                    console.log('you need to select the mail type')
                }
            })

        } else {
            $('#email_type').remove()
            $('#email_template_id').remove();
            $('#email_subject').remove()
            $('#message').remove();
           
        }
    })

    $("form").submit(function (e) {
        if ($('#email_type').val() === 'template' && $('#email_template_id').val() === '') {
            alert('You must select an email template');
            e.preventDefault(e);
            return;
        }

        if ($('#email_subject').val() === '') {
            alert('Please enter mail subject');
            e.preventDefault(e);
            return;
        }
    });




})