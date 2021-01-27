<?php

namespace App\Services\common;


use Illuminate\Support\Facades\DB;

class ImageRouteService
{

	public static function get_dir_name($real_path)
	{
		return dirname($real_path);

	}

	public static function get_file_name($real_path)
	{
		return basename($real_path);

	}

	public static function get_save_dir($dir_name)
	{
		return public_path($dir_name);
	}

	public static function get_file_array($file_name)
	{
		$file_name_array = explode('.',$file_name);

		return $file_name_array;
	}

	public static function get_save_file_path($save_dir,$file_prefix,$want_ratio,$want_width,$want_height,$water,$extension)
	{
		$save_file_path = $save_dir."/".$file_prefix."_".$want_ratio."_".$want_width."_".$want_height."_".$water.".".$extension;

		return $save_file_path;
	}

	public static function get_save_file_name($file_prefix,$want_ratio,$want_width,$want_height,$water,$extension)
	{
		$save_file_name = $file_prefix."_".$want_ratio."_".$want_width."_".$want_height."_".$water.".".$extension;

		return $save_file_name;
	}
}