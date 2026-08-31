<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Roles and permissions.
 *
 * Permissions are the unit of authorisation, roles are just bundles of them.
 * That way a new capability is added in one place and handed to whichever roles
 * should have it, rather than being scattered through route checks.
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'post.view', 'post.create', 'post.update.own', 'post.update.any',
            'post.delete', 'post.publish',
            'media.upload', 'media.delete',
            'comment.moderate',
            'subscriber.view', 'subscriber.export', 'newsletter.send',
            'user.manage', 'settings.manage',
        ];

        foreach ($permissions as $name) {
            Permission::findOrCreate($name);
        }

        $roles = [
            // Everything, including who else gets in.
            'admin' => $permissions,

            // Runs the publication: can publish anyone's work and moderate.
            'editor' => [
                'post.view', 'post.create', 'post.update.own', 'post.update.any',
                'post.delete', 'post.publish',
                'media.upload', 'media.delete',
                'comment.moderate',
                'subscriber.view', 'newsletter.send',
            ],

            // Writes and publishes their own work only.
            'author' => [
                'post.view', 'post.create', 'post.update.own', 'post.publish',
                'media.upload',
            ],

            // Signs in on the public site to comment. No panel access.
            'subscriber' => [],
        ];

        foreach ($roles as $role => $granted) {
            Role::findOrCreate($role)->syncPermissions($granted);
        }
    }
}
