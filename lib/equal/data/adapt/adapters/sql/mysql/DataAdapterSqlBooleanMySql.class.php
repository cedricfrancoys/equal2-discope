<?php
/*
    This file is part of the eQual framework <https://github.com/equalframework/equal>
    Some Rights Reserved, Cedric Francoys, 2010-2024
    Licensed under GNU LGPL 3 license <http://www.gnu.org/licenses/>
*/
namespace equal\data\adapt\adapters\sql\mysql;

use equal\data\adapt\adapters\sql\DataAdapterSqlBoolean;

class DataAdapterSqlBooleanMySql extends DataAdapterSqlBoolean {

    public function getType() {
        return 'sql/boolean';
    }

    public function castInType(): string {
        return 'boolean';
    }

    public function castOutType($usage=null): string {
        return 'TINYINT(4)';
    }

}