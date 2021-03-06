<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    /**
     * @var string
     */
    protected $table = 'role_user';

    public static function getRoleForUser($userId) {
    	$data = parent::where('user_id', $userId)->get();
    	$results = [];
    	foreach ($data as $key => $value) {
    		$results[] = $value->role_id;
    	}
    	return $results;
    }

    public static function roleWordList($userId) {
        $data = self::getRoleForUser($userId);
        $word = [];
        foreach ($data as $key => $value) {
            $role = Role::find($value);
            if(isset($role->display_name)) {
                $word[] = $role->display_name;
            }
        }

        return implode(',', $word);
    }
}
