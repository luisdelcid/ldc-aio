<?php

if(class_exists('RWMB_Field')){
    class RWMB_Row_Open_Field extends RWMB_Field {
        public static function html($meta, $field){
            return '';
        }
    }
    class RWMB_Row_Close_Field extends RWMB_Field {
        public static function html($meta, $field){
            return '';
        }
    }
    class RWMB_Col_Open_Field extends RWMB_Field {
        public static function html($meta, $field){
            return '';
        }
    }
    class RWMB_Col_Close_Field extends RWMB_Field {
        public static function html($meta, $field){
            return '';
        }
    }
    class RWMB_Raw_Html_Field extends RWMB_Field {
        public static function html($meta, $field){
            return '';
        }
    }
}
