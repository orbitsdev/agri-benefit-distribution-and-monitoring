<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use App\Models\Support;
use App\Models\Barangay;
use App\Models\Personnel;
use App\Models\Transaction;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Spatie\MediaLibrary\InteractsWithMedia;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser, HasMedia {
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use InteractsWithMedia;


    public function getCoverUrlAttribute(): ?string
    {
      return self::getImage();
    }
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
        // return match($panel->getId()){
        //     'admin'=> $this->hasAnyRole(['Admin']),
        //     'clinic'=> $this->hasAnyRole(['Admin','Veterenarian']),
        //     'client'=> $this->hasAnyRole(['Admin','Client','Veterenarian']),
        // };
    }


    public const SUPER_ADMIN = 'Super Admin';
    public const ADMIN = 'Admin';
    public const MEMBER = 'Member';


    public const ROLE_OPTIONS = [
        // self::SUPER_ADMIN => self::SUPER_ADMIN,
        self::ADMIN => self::ADMIN,
        self::MEMBER => self::MEMBER,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();

    }

    public static function scopeIsNotSuperAdmin($query)
    {
        return $query->where('email', '!=', 'superadmin@gmail.com')->where('role', '!=', self::SUPER_ADMIN);
    }

    public function getImage()
    {
        if ($this->hasMedia()) {
            return $this->getFirstMediaUrl();
        }

        return asset('images/placeholder-image.jpg');
    }

    public function barangay(){
        return $this->belongsTo(Barangay::class);
    }

    // has many personnels

    public function personnels()
    {
        return $this->hasMany(Personnel::class);
    }
    public function getHasPersonnelAttribute(): bool
    {
        return $this->personnels()->exists();
    }


    // scope where doesnt have the same barnaggau personne
    public function scopeNotRegisteredInSameBarangay($query, $barangay_id)
    {
        return $query->whereDoesntHave('personnels', function ($query) use ($barangay_id) {
            $query->where('barangay_id', $barangay_id);
        });
    }


    public function transactions()
    {
        return $this->hasMany(Transaction::class,'admin_id') ;
    }



    public function scopeIsMember($query)
    {
        return $query->where('role', self::MEMBER);
    }
    public function isAccountIsMember($query)
    {
        return $query->where('role', self::MEMBER);
    }

    //scope by barangay
    public function scopeByBarangay($query, $barangay_id){
        return $query->where('barangay_id', $barangay_id);
    }

    //scope is not admin
    public function scopeIsNotAdmin($query){
        return $query->where('role', '!=', self::ADMIN);
    }



    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for inactive users
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function  support(){
        return Support::where('unique_code', $this->code)->first();
    }


}
