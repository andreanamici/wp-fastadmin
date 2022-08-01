(function(w,$){

    "use strinct";

    /**
     * Decode HTML to string
     *
     * @param {string} value
     *
     * @returns {string}
     */
    w.wp_fa_html_decode = function(value)
    {
        return $("<textarea/>").html(value).text();
    }

    /**
     * Encode string to HTML
     *
     * @param {string} value
     *
     * @returns {string}
     */
    w.wp_fa_html_encode = function(value)
    {
        return $('<textarea/>').text(value).html();
    }

    /**
     * Transform form data array as form data object
     *
     * @param {array} formArrayData
     *
     * @returns {object}
     */
    w.wp_fa_form_array_to_object = function(formArrayData)
    {
        var formData = {};

        for (var i = 0; i < formArrayData.length; i++)
        {
           var value = formArrayData[i]['value'];

           if(typeof formData[formArrayData[i]['name']] != 'undefined')
           {
               var curr_value = formData[formArrayData[i]['name']];
               if(typeof curr_value != 'object'){
                   curr_value = [curr_value];
               }
               curr_value.push(value);
               value = curr_value;
           }

           formData[formArrayData[i]['name']] = value;
        }
        return formData;
    }

    /**
     * Send ajax request GET
     *
     * @param string action
     * @param object data
     * @param function onSuccess
     * @param function onError
     *
     * @returns {jqXHR}
     */
    w.wp_fa_ajax_get = async function(action, data)
    {
        return wp_fa_ajax('GET',action, data);
    }

    /**
     * Send ajax request POST
     *
     * @param string action
     * @param object data
     * @param function onSuccess
     * @param function onError
     *
     * @returns {jqXHR}
     */
    w.wp_fa_ajax_post = async function(action, data)
    {
        return wp_fa_ajax('POST',action, data);
    }


    /**
     * Send ajax request
     *
     * @param string method
     * @param string action
     * @param object data
     * @param function onSuccess
     * @param function onError
     *
     * @returns {jqXHR}
     */
    w.wp_fa_ajax = async function(method, action, data)
    {
        method    = typeof method    == 'undefined' ? 'GET'     : method;
        data      = typeof data      == 'undefined' ? {}        : data;

        data['action'] = action;

        //console.log('ps ajax ['+method+'] url: '+ajaxurl+' data: ',data);

        return new Promise((resolve, reject) => {
            $.ajax({
                url: ajaxurl,
                method: method,
                data: data,
                success: resolve,
                error: reject,
                dataType: 'json'
            });
        });
    }

})(window,jQuery);