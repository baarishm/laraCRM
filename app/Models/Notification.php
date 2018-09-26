<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class Notification extends Model {

    protected $table = 'notifications';
    protected $fillable = ['display_data', 'display_to', 'read_at', 'created_at'];

    public static function markAsRead($id) {
        DB::table('notifications')->where('id', $id)->whereNull('read_at')->update(['read_at', date('Y-m-d H:i:s')]);
        return true;
    }
    
    public static function markAllAsRead($count = '') {
        $query = DB::table('notifications')->where('display_to', Auth::user()->context_id)->whereNull('read_at')->orderBy('id','desc');
        if ($count != '') {
            $query->take($count);
        }
        $query->update(['read_at' => date('Y-m-d H:i:s')]);
        return "Records marked as read!";
    }

    public static function unreadNotifications($count = '') {
        $query = DB::table('notifications')->where('display_to', Auth::user()->context_id)->whereNull('read_at')->orderBy('id','desc');
        if ($count != '') {
            $query->take($count);
        }
        return $query->get();
    }

    public static function readNotifications($count = '') {
        $query = DB::table('notifications')->where('display_to', Auth::user()->context_id)->whereNotNull('read_at')->orderBy('id','desc');
        if ($count != '') {
            $query->take($count);
        }
        return $query->get();
    }

}
