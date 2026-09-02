<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Panel;
use Filament\Models\Contracts\FilamentUser;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'avatar_path'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Who may open the admin panel. Subscribers sign in on the public site but
     * never reach the panel; authors, editors and admins do, and what they can
     * do once inside is decided by permissions rather than by this gate.
     */
    /**
     * The author's photograph, if they have uploaded one.
     *
     * Null is a perfectly good answer: the avatar component falls back to
     * initials on a coloured disc, which is better than a grey silhouette and
     * better than reaching out to Gravatar with a hash of somebody's email.
     */
    public function avatarUrl(int $size = 96): ?string
    {
        return Rendition::url($this->avatar_path, $size);
    }

    public function avatarSrcset(): ?string
    {
        return Rendition::srcset($this->avatar_path);
    }

    public function posts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(['admin', 'editor', 'author']);
    }

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
}
