
var fa_translations = typeof fa_translations != "undefined" ? fa_translations : {};

function fa_translate(string)
{
    return  typeof fa_translations[string] != "undefined" ? fa_translations[string]  : string;
}

function fa_alert_dialog(message, title, onConfirm)
{
    var $confirmbox = jQuery("#dialog-confirmbox").length ? jQuery("#dialog-confirmbox") : jQuery("<div/>").attr('id', 'dialog-confirmbox');
    var title = typeof title == "string" ? title : fa_translate('Alert');

    if (jQuery("#dialog-confirmbox").length == 0)
    {
        jQuery("body").append($confirmbox);
    }

    var onConfirm = typeof onConfirm == "function" ? onConfirm : function () {};

    var options = jQuery.dg.error_dialog;
    options.title = title;

    $confirmbox.text(message);
    $confirmbox.data({action: 'callback', callback: onConfirm, callback_cancel: function () {}}).dialog(options).dialog('open');
}

function fa_confirm_dialog(title, message, onConfirm, onCancel)
{
    var $confirmbox = jQuery("#dialog-confirmbox").length ? jQuery("#dialog-confirmbox") : jQuery("<div/>").attr('id', 'dialog-confirmbox');

    if (jQuery("#dialog-confirmbox").length == 0)
    {
        jQuery("body").append($confirmbox);
    }

    var options = jQuery.dg.base_dialog;
    options.title = title;

    $confirmbox.text(message);
    $confirmbox.data({action: 'callback', callback: onConfirm, callback_cancel: onCancel}).dialog(options).dialog('open');
}

function fa_init_dialogs()
{
    jQuery.dg = {
        base_dialog : {
            autoOpen: false,
            closeOnEscape: true,
            draggable: false,
            modal: true,
            buttons: [
                {
                    text: fa_translate("Ok"),
                    click: function ()
                    {
                        var action = jQuery(this).data('action');
                        if (action == 'redirect')
                        {
                            var url = jQuery(this).data('url');
                            var params = jQuery(this).data('params');
                            if (params !== undefined && params !== null)
                            {
                                url += '?' + params;
                            }
                            jQuery(location).attr('href', url);

                            jQuery(this).dialog("close");
                        } 
                        else if (action == 'submit')
                        {
                            var form_id = jQuery(this).data('form_id');
                            jQuery("#" + form_id).submit();

                            jQuery(this).dialog("close");
                        } 
                        else if (action == 'callback')
                        {
                            jQuery(this).dialog("close");
                            callback = jQuery(this).data('callback');
                            if (typeof callback === 'function')
                            {
                                callback();
                            }
                            else if(typeof window[callback] == "function")
                            {
                                window[callback]();
                            }
                        }
                    }
                },
                {
                    text: fa_translate('Cancel'),
                    click: function ()
                    {
                        jQuery(this).dialog("close");
                        callback_cancel = jQuery(this).data('callback_cancel');
                        if (typeof callback_cancel === 'function')
                        {
                            callback_cancel();
                        }
                        else if(typeof window[callback_cancel] == "function")
                        {
                            window[callback_cancel]();
                        }
                    }
                }
            ],
            resizable: false,
            open: function () {
                // scrollbar fix for IE
                jQuery('body').css('overflow', 'hidden');                
            },
            close: function () {
                // reset overflow
                jQuery('body').css('overflow', 'auto');
            }
        },

        error_dialog : {
            autoOpen: true,
            closeOnEscape: true,
            draggable: false,
            modal: true,
            buttons: [
                {
                    text: fa_translate("Ok"),
                    click: function ()
                    {
                        jQuery(this).dialog("close");
                        callback = jQuery(this).data('callback');
                        if (typeof callback === 'function')
                        {
                            callback();
                        }
                    }
                }
            ],
            resizable: false,
            open: function () {
                // scrollbar fix for IE
                jQuery('body').css('overflow', 'hidden');
            },
            close: function () {
                // reset overflow
                jQuery('body').css('overflow', 'auto');
            }
        }
    };
    
    jQuery("#deletebox").dialog(jQuery.dg.base_dialog);
    jQuery("#confirmbox").dialog(jQuery.dg.base_dialog);
}


function fa_init_datepicker()
{
    var datepicker  = {
        'it_IT' : {
            closeText: "Chiudi",
            prevText: "&#x3C;Prec",
            nextText: "Succ&#x3E;",
            currentText: "Oggi",
            monthNames: ["Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno","Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"],
            monthNamesShort: ["Gen", "Feb", "Mar", "Apr", "Mag", "Giu","Lug", "Ago", "Set", "Ott", "Nov", "Dic"],
            dayNames: ["Domenica", "Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato"],
            dayNamesShort: ["Dom", "Lun", "Mar", "Mer", "Gio", "Ven", "Sab"],
            dayNamesMin: ["Do", "Lu", "Ma", "Me", "Gi", "Ve", "Sa"],
            weekHeader: "Sm",
            dateFormat: "dd/mm/yy",
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ""
        }
    };
    
    jQuery.timepicker.regional['it_IT'] = {
            timeOnlyTitle: 'Scegli orario',
            timeText: 'Orario',
            hourText: 'Ora',
            minuteText: 'Minuti',
            secondText: 'Secondi',
            millisecText: 'Millisecondi',
            microsecText: 'Microsecondi',
            timezoneText: 'Fuso orario',
            currentText: 'Adesso',
            closeText: 'Chiudi',
            timeFormat: 'HH:mm',
            timeSuffix: '',
            amNames: ['m.', 'AM', 'A'],
            pmNames: ['p.', 'PM', 'P'],
            isRTL: false
    };
    
    if(typeof datepicker[wp_locale] != 'undefined'){
        jQuery.datepicker.setDefaults(datepicker[wp_locale]);
    }
    
    jQuery(".fa-datepicker").each(function(){
        if(!jQuery(this).data('datepicker'))
        {
            jQuery(this).datepicker({
                changeMonth: true,
                changeYear: true,
                yearRange: "-70:+5",
                dateFormat: 'dd/mm/yy',
                onSelect: function(dateText,inst){
                    jQuery(this).trigger('onSelect', [dateText,inst]);
                }
            })
            var val = jQuery(this).attr("value");
            jQuery(this).datepicker('option', 'constrainInput', true);
            jQuery(this).datepicker('setDate', val);
        }
    });
    
    if(typeof jQuery.timepicker.regional[wp_locale] != 'undefined')
    {
        var datetimepicker_configuration = jQuery.extend(typeof datepicker[wp_locale] != 'undefined' ? datepicker[wp_locale] : {}, {showTime: true, controlType: 'select'});
        jQuery.timepicker.setDefaults(jQuery.timepicker.regional[wp_locale]);
    }
    
    jQuery(".fa-datetimepicker").each(function(key,value)
    {
        var val = jQuery(this).attr("value");
        datetimepicker_configuration['onSelect'] = function(dateText,inst){ 
            jQuery(this).trigger('onSelect', [dateText,inst]);
        };
        jQuery(this).datetimepicker(datetimepicker_configuration);
        jQuery(this).datetimepicker('option', 'showOn', 'focus');
        jQuery(this).datetimepicker('option', 'buttonImage',"");
        jQuery(this).datetimepicker('option', 'buttonImageOnly',false);		
        jQuery(this).datetimepicker('option', 'constrainInput', false);	
        
        if (val != "")
        {	
            jQuery(this).datetimepicker('setDate', val);
        }	
    });
}

function fa_init_tooltip()
{
    jQuery('.fa-tooltip').each(function(){
       jQuery(this).tooltip(); 
    });
}


function fa_init_combobox()
{
    jQuery(".fa-combobox").each(function(){
       jQuery(this).combobox(); 
    });
}

function fa_init_confirmbox()
{
    jQuery(".fa-confirmbox").bind('click', function(){
        
        var rel      = jQuery(this).attr('rel');
        var url      = jQuery(this).attr('href') ? jQuery(this).attr('href') : jQuery(this).data('href');
        var action   = jQuery(this).data("action") ? jQuery(this).data("action") : 'redirect';
        var callback = jQuery(this).data("callback") ? jQuery(this).data("callback") : null;
        
        if (rel != '' && rel !== undefined)
        {
           jQuery("#" + rel).data({'url' : url, 'action' : action , 'callback': callback }).dialog('open');			
        }	
        else
        {
            var message = jQuery(this).data("confirmText")  ?  jQuery(this).data("confirmText")  : fa_translate('Are you sure?');
            var title   = jQuery(this).data("confirmTitle") ?  jQuery(this).data("confirmTitle") : fa_translate('Confirm');
            fa_confirm_dialog(title,message, function(){ 
                if(action == 'redirect'){
                    window.location.href = url; 
                }else{
                    typeof callback == 'function' ? callback() : (typeof window[callback] == 'function' ? window[callback]() : jQuery(this).trigger("confirm"));
                }
                return false;
            });
        }
        return false;
    });		
}


/**
 * Transform form data array as form data object
 * 
 * @param {array} formArrayData
 * 
 * @returns {object}
 */
function fa_form_array_to_object(formArrayData)
{
  var formData = {};
  for (var i = 0; i < formArrayData.length; i++){
    formData[formArrayData[i]['name']] = formArrayData[i]['value'];
  }
  return formData;
}

/**
 * Send ajax request GET to PsSurveyPostTypeAjax
 * 
 * @param string page
 * @param object data
 * @param function onSuccess
 * @param function onError
 * 
 * @returns {jqXHR}
 */
function fa_ajax_get(page, data, onSuccess, onError)
{
    var args = (arguments.length === 1 ? [arguments[0]] : Array.apply(null, arguments));
    args.unshift('GET');
    return fa_ajax.apply(this,args);
}

/**
 * Send ajax request POST to PsSurveyPostTypeAjax
 * 
 * @param string page
 * @param object data
 * @param function onSuccess
 * @param function onError
 * 
 * @returns {jqXHR}
 */
function fa_ajax_post(page, data,  onSuccess, onError)
{
    var args = (arguments.length === 1 ? [arguments[0]] : Array.apply(null, arguments));
    args.unshift('POST');
    return fa_ajax.apply(this, args);
}


/**
 * Send ajax request to PsSurveyPostTypeAjax
 * 
 * @param string method
 * @param string page
 * @param object data
 * @param function onSuccess
 * @param function onError
 * 
 * @returns {jqXHR}
 */
function fa_ajax(method, page, data, onSuccess, onError)
{
    var method    = typeof method    == 'undefined' ? 'GET'     : method;
    var onSuccess = typeof onSuccess == 'function'  ? onSuccess : function(data){};
    var onError   = typeof onError   == 'function'  ? onError   : function(data){};
    var data      = typeof data      == 'undefined' ? {}        : data;
    
    data['page'] = page;
        
    return jQuery.ajax({
        url: pageurl+'?page='+page,
        method: method,
        data: data,
        success: function(response){
            onSuccess(response);
        },
        error: function(response){ 
            onError(response);
        },
        dataType: 'json'
    });
}

function fa_init_dom()
{
    var $ = typeof $ == 'undefined' ? jQuery : $;
    
    jQuery("body").on('submit','.fa-form', function(e){
            jQuery(this).find("input[type=submit]").attr("disabled", true);
    });

    jQuery("body").on('keyup','.fa-has-error', function(e){
       jQuery(this).removeClass('fa-has-error');
       jQuery(this).next('.fa-form-field-errors').remove();
       if(jQuery('.fa-has-error').length == 0){
           jQuery('.fa-messages.error').remove();
       }
    });

    fa_init_datepicker();

    fa_init_dialogs();

    fa_init_confirmbox();

    fa_init_tooltip();
    
    fa_init_combobox();
}