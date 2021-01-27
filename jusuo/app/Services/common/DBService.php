<?php
/**
 * Created by PhpStorm.
 * User: libin
 * Date: 2019/1/18
 * Time: 14:17
 */

namespace App\Http\Services\common;


use Illuminate\Support\Facades\DB;

class DBService
{

    //同时更新多个记录，参数，表名，数组（别忘了在一开始use DB;）
    public static function updateBatch($tableName = "", $multipleData = array()){

        if( $tableName && !empty($multipleData) ) {

            // column or fields to update
            $updateColumn = array_keys($multipleData[0]);
            $referenceColumn = $updateColumn[0]; //e.g id
            unset($updateColumn[0]);
            $whereIn = "";

            $q = "UPDATE ".$tableName." SET ";
            foreach ( $updateColumn as $uColumn ) {
                $q .=  $uColumn." = CASE ";

                foreach( $multipleData as $data ) {
                    $q .= "WHEN ".$referenceColumn." = ".$data[$referenceColumn]." THEN '".$data[$uColumn]."' ";
                }
                $q .= "ELSE ".$uColumn." END, ";
            }
            foreach( $multipleData as $data ) {
                $whereIn .= "'".$data[$referenceColumn]."', ";
            }
            $q = rtrim($q, ", ")." WHERE ".$referenceColumn." IN (".  rtrim($whereIn, ', ').")";
                  // Update
            return DB::update(DB::raw($q));

        } else {
            return false;
        }

    }


}