<?php
/*
    This file is part of the eQual framework <https://github.com/equalframework/equal>
    Some Rights Reserved, Cedric Francoys, 2010-2024
    Licensed under GNU LGPL 3 license <http://www.gnu.org/licenses/>
*/
namespace equal\data\adapt\adapters\json;

use equal\data\adapt\DataAdapter;

class DataAdapterJsonDateYear implements DataAdapter {

    public function getType() {
        return 'json/date.year';
    }

    public function castInType(): string {
        return 'integer';
    }

    public function castOutType($usage=null): string {
        return 'Number';
    }

    /**
     * Handles the conversion to the PHP type equivalent.
     * Adapts the input value from JSON type to PHP type (JSON -> PHP).
     *
     * @param mixed         $value      Value to be adapted.
     * @param string|Usage  $usage      The usage descriptor the adaptation is requested for.
     * @param string        $locale     The locale to be used for adaptation.
     * @return mixed
     */
	public function adaptIn($value, $usage, $locale='en') {
        $result = null;
        if(!is_null($value)) {
            if(is_numeric($value)) {
                // value is an integer (integer 0-9999)
                $result = intval($value);
            }
            else {
                // date/month
                $result = date('Y', strtotime($value));
            }
        }
        return $result;
    }

    /**
     * Handles the conversion to the type targeted by the DataAdapter.
     * Adapts the input value from PHP type to JSON type (PHP -> JSON).
     *
     * @param mixed         $value      Value to be adapted.
     * @param string|Usage  $usage      The usage descriptor the adaptation is requested for.
     * @param string        $locale     The locale to be used for adaptation.
     * @return mixed
     */
    public function adaptOut($value, $usage, $locale='en') {
        if(is_null($value)) {
            return null;
        }
        return intval(($value <= 2100 && $value >= 1970)?$value:date('Y', $value));
    }

}
