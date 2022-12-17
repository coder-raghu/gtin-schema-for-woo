jQuery(function ($) {
    "use strict";
    
    var _body = $('body'),
        _form = $('.variations_form'),
        _code = _form.closest('.summary').find('.gtin-schema'),
        _reset = _code.length > 0 ? _code.text() : '',
        _parent_code = _code.parent();

     $.fn.gtin_schema_variations = function() {

        _form.on( 'found_variation', function( event, variation ){
            if ( variation._gtin_schema_code ) {
                _code.text( variation._gtin_schema_code );
                _parent_code.show();
            } else {
                _code.gtin_schema_reset_content();
            }
        });

        _form.on( 'reset_data', function(){
            $.fn.gtin_schema_reset_content();
        });

    };

    $.fn.gtin_schema_reset_content = function(){
        if( _reset !== _code.text() ){
            _code.text(_reset);
        }
    };

    if( _body.hasClass('single-product') ){        
        $.fn.gtin_schema_variations();
    }

});