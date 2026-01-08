<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravolt\Avatar\Avatar;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
        'avatar',
        'google_id',
        'role_id',
        'hubspot_id',
        'hubspot_token',
        'hubspot_refreshToken',
        //'organization_id',
        'google_token',
        'google_refresh_token',
        'google_name',
        'google_calendar_id',
        'timezone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google_id',
        'hubspot_id',
        'hubspot_token',
        'hubspot_refreshToken'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'google_token' => 'json',
        'google_refresh_token' => 'json',
    ];

    public function getAvatar(): string {
        if (!empty($this->avatar) && Storage::disk('public')->exists('avatars/'.$this->avatar))
            return Storage::disk('public')->url('avatars/'.$this->avatar);
        else
            return asset('images/avatar.jpg');
    }

    public function createAvatar(){
        $filename = uniqid() . '-' . now()->timestamp . '.png';
        $avatar = new Avatar(config('laravolt.avatar') );
        $avatar->create($this->name)->save(Storage::disk('public')->path('avatars'). '/'. $filename);
        $this->avatar = $filename;
        $this->save();
    }

    protected static function booted(){
        /*
        self::creating(function($user){
            $var = explode('@', $user->email);
            $domain_name =  array_pop($var);
            $organization = Organization::where('name', $domain_name )->first();
            if ($organization) {
              if(!$user->role_id) $user->role_id=2;
            }else{
                $organization = Organization::create([
                    'name'=>$domain_name,
                    'currency'=> 'EUR'
                ]);
                if(!$user->role_id) $user->role_id=3;
            }
            $user->organization_id = $organization->id;
        });*/

        static::created(function($user){
            $user->createAvatar();
        });

        static::deleting(function(User $user) {
            Storage::disk('public')->delete('avatars/'. $user->avatar);
        });
    }

    public function setImpersonating($id)
    {
        Session::put('impersonate', $id);
    }

    public function stopImpersonating()
    {
        Session::forget('impersonate');
    }

    public function isImpersonating() : bool
    {
        return Session::has('impersonate');
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function isAdmin() : bool
    {
        return $this->role->id == 1;
    }

    public function organizations()
    {
        return $this->belongsToMany('App\Models\Organization');
    }

    public function organization()
    {
        //return $this->belongsTo('App\Models\Organization');
        $hubspot_portalId = session('hubspot_portalId');
        //if ($hubspot_portalId) return $this->organizations()->where('hubspot_portalId', $hubspot_portalId);
        //else return null
        //return Cache::remember('organization', 86400, function () use($hubspot_portalId) {
            //return $this->organizations()->where( 'hubspot_portalId', $hubspot_portalId )->first();
        //});
        if( !Session::has('organization') )
            Session::put('organization', $this->organizations()->where( 'hubspot_portalId', $hubspot_portalId )->first());
        return Session::get('organization');
    }

    public function currency(){
        $organization = $this->organization();
        if( !Session::has('user_currency') && $organization )
            Session::put('user_currency', $organization->currency);
        return Session::get('user_currency', 'USD'); // Default to USD if no organization
        //return $this->organization()->first()->currency;
    }

    public function member(){
         $organization = $this->organization();
         if (!$organization) {
             return Member::whereRaw('1 = 0'); // Return empty query if no organization
         }
         return Member::where('email', $this->email)->where('organization_id', $organization->id);
    }


}
