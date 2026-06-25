<?php
/**
 * 种子脚本：插入测试用户
 * 运行: php webman seed:user
 */

use support\Db;
use support\console\Command;

class SeedUser extends Command
{
    protected string $description = 'Insert test user (admin / 123456)';

    public function handle(): void
    {
        $exists = Db::table('users')->where('username', 'admin')->exists();
        if ($exists) {
            $this->info('Test user already exists.');
            return;
        }

        Db::table('users')->insert([
            'username'   => 'admin',
            'password'   => password_hash('123456', PASSWORD_BCRYPT),
            'name'       => '管理员',
            'is_admin'   => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->info('Test user created: admin / 123456');
    }
}
