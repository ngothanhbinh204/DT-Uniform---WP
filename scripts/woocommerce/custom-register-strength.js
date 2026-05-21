jQuery(function ($) {
    'use strict';

    var CustomRegisterStrength = {

        init: function () {
            $(document.body).on('keyup change', '.custom_registration_form #reg_password', this.strengthMeter);
        },

        strengthMeter: function () {
            var field      = $('.custom_registration_form #reg_password'),
                fieldValue = field.val(),
                wrapper    = field.closest('div.form-row'),
                strength   = 1;

            CustomRegisterStrength.includeMeter(wrapper, field);

            if (!fieldValue) return;

            strength = CustomRegisterStrength.checkPasswordStrength(wrapper, field);

            var submit      = $('.custom_registration_form button[type="submit"]'),
                minStrength = (typeof wc_password_strength_meter_params !== 'undefined')
                    ? wc_password_strength_meter_params.min_password_strength
                    : 3;

            if (fieldValue.length > 0 && strength < minStrength && strength !== -1) {
                submit.attr('disabled', 'disabled').addClass('disabled');
            } else {
                submit.prop('disabled', false).removeClass('disabled');
            }
        },

        includeMeter: function (wrapper, field) {
            var meter = wrapper.find('.woocommerce-password-strength');

            if ('' === field.val()) {
                meter.hide();
                field.removeAttr('aria-describedby');
                return;
            }

            if (0 === meter.length) {
                field.after('<div id="custom_password_strength" class="woocommerce-password-strength" role="alert"></div>');
                field.attr('aria-describedby', 'custom_password_strength');
            } else {
                meter.show();
            }
        },

        checkPasswordStrength: function (wrapper, field) {
            var meter    = wrapper.find('.woocommerce-password-strength'),
                strength = wp.passwordStrength.meter(
                    field.val(),
                    wp.passwordStrength.userInputDisallowedList()
                ),
                minStrength = (typeof wc_password_strength_meter_params !== 'undefined')
                    ? wc_password_strength_meter_params.min_password_strength
                    : 3,
                error = strength < minStrength
                    ? ' - ' + (typeof wc_password_strength_meter_params !== 'undefined'
                        ? wc_password_strength_meter_params.i18n_password_error
                        : 'Mật khẩu quá yếu')
                    : '',
                hint_html = '<small class="woocommerce-password-hint">'
                    + (typeof wc_password_strength_meter_params !== 'undefined'
                        ? wc_password_strength_meter_params.i18n_password_hint
                        : 'Gợi ý: dùng chữ hoa, số và ký tự đặc biệt.')
                    + '</small>';

            meter.removeClass('short bad good strong');
            wrapper.find('.woocommerce-password-hint').remove();

            switch (strength) {
                case 0:
                    meter.addClass('short').html((typeof pwsL10n !== 'undefined' ? pwsL10n['short'] : 'Quá ngắn') + error);
                    meter.after(hint_html);
                    break;
                case 1:
                case 2:
                    meter.addClass('bad').html((typeof pwsL10n !== 'undefined' ? pwsL10n.bad : 'Yếu') + error);
                    meter.after(hint_html);
                    break;
                case 3:
                    meter.addClass('good').html(typeof pwsL10n !== 'undefined' ? pwsL10n.good : 'Trung bình');
                    break;
                case 4:
                    meter.addClass('strong').html(typeof pwsL10n !== 'undefined' ? pwsL10n.strong : 'Mạnh');
                    break;
                case 5:
                    meter.addClass('short').html(typeof pwsL10n !== 'undefined' ? pwsL10n.mismatch : 'Không khớp');
                    break;
            }

            return strength;
        }
    };

    CustomRegisterStrength.init();
});