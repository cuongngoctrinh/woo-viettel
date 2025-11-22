/**
 * ViettelPost Checkout Script - FINAL FIX 2025
 * Chống vòng lặp update_checkout + auto select ổn định 100%
 */

(function ($) {
    "use strict";

    var VTP = {
        calculating: false,
        updating: false,
        selected: false,        // Đánh dấu đã chọn thành công 1 lần
        lastCalcKey: null,
        preventLoopTimeout: null,

        init: function () {
            if (!$('body').hasClass('woocommerce-checkout')) return;

            $(document.body)
                .on('viettelpost_address_complete', $.proxy(this.onAddressReady, this))
                .on('updated_checkout', $.proxy(this.onCheckoutUpdated, this))
                .on('update_checkout', $.proxy(this.onUpdateStart, this));
        },

        onAddressReady: function (e, data) {
            if (this.calculating || this.updating || this.selected) return;

            var key = data.provinceId + '-' + data.districtId + '-' + data.wardId;
            if (this.lastCalcKey === key) return;

            this.lastCalcKey = key;
            this.calculateShipping(data.provinceId, data.districtId, data.wardId);
        },

        calculateShipping: function (provinceId, districtId, wardId) {
            if (this.calculating || this.selected) return;

            this.calculating = true;
            this.showMessage('Đang tính phí vận chuyển ViettelPost...', 'loading');

            $.ajax({
                url: wc_viettelpost_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'viettelpost_calculate_shipping',
                    nonce: wc_viettelpost_params.nonce,
                    province_id: provinceId,
                    district_id: districtId,
                    ward_id: wardId
                },
                success: (res) => {
                    this.hideMessage();

                    if (!res.success || !res.data || !res.data.rates) {
                        this.showMessage('Không lấy được phí ship ViettelPost', 'error');
                        this.calculating = false;
                        return;
                    }

                    // Thành công → trigger update_checkout 1 lần duy nhất
                    this.updating = true;
                    $(document.body).trigger('update_checkout');
                },
                error: () => {
                    this.hideMessage();
                    this.showMessage('Lỗi kết nối ViettelPost', 'error');
                    this.calculating = false;
                }
            });
        },

        onUpdateStart: function () {
            this.updating = true;
        },

        onCheckoutUpdated: function () {
            // Chỉ chạy 1 lần duy nhất sau khi có rate
            if (this.selected || this.calculating) {
                this.updating = false;
                return;
            }

            setTimeout(() => {
                var $vtp = $('input[value*="viettelpost"]:visible').first();

                if ($vtp.length && !$vtp.is(':checked')) {
                    $vtp.prop('checked', true).trigger('change');
                    this.selected = true;  // Đánh dấu đã chọn → chặn mọi tính toán sau
                    this.showMessage('Đã chọn ViettelPost - Phí: ' + $vtp.parents('li').find('.amount').text(), 'success');
                    console.log('ViettelPost: Đã chọn tự động & chặn loop thành công');
                }

                this.updating = false;
            }, 500);
        },

        showMessage: function (msg, type = 'info') {
            this.hideMessage();
            var color = type === 'loading' ? '#2271b1' : (type === 'success' ? '#00a32a' : '#d63638');
            $('#shipping_method').before(
                `<div class="vtp-msg" style="padding:12px; margin:10px 0; background:#f9f9f9; border-left:4px solid ${color}; font-weight:500;">
                    ${type === 'loading' ? '<span class="spinner is-active" style="float:left;margin-right:10px;"></span>' : ''}
                    ${msg}
                </div>`
            );
        },

        hideMessage: function () {
            $('.vtp-msg').remove();
        }
    };

    $(document).ready(function () {
        VTP.init();
    });

})(jQuery);